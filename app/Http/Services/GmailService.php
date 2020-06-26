<?php

namespace App\Http\Services;

use App\Http\Services\UserService;
use App\Http\Services\SettingService;
use App\Http\Services\SubscriptionService;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\MailboxController;

use App\Http\Repositories\LeadRepository;
use App\Http\Repositories\ClientRepository;
use App\Http\Repositories\CompanyRepository;

use Ddomanskyi\LaravelGmail\Services\Message\Mail;

use Carbon\Carbon;
use Swift_Message;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class GmailService extends GmailController
{
    public $service;
    public $fileName;
    public $swiftMessage;

    public function __construct($fileName = null)
	{
        parent::__construct();

        $this->fileName = $fileName;
        $this->swiftMessage = new Swift_Message();
        $tokenPath = './storage/app/gmail/tokens/' . $fileName . '.json';
        
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        if ($this->client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                dd('Could not refresh token');
            }
        }

        $this->service = new \Google_Service_Gmail($this->client);
        $this->check();
	}

	public function check()
	{
        $firstPhrase = 'New Message:';
        $SecondPhrase = 'is requesting a quote from';
        $param = array();
        $param['q'] = 'is:unread';
        $messagesResponse = $this->service->users_messages->listUsersMessages('me', $param);
        $messages = $messagesResponse->getMessages();

        foreach ($messages as $message) {
            $to = '';
            $arr = [];
            $optParams = array();
            $optParams['format'] = 'raw';
            $optParams['fields'] = 'raw';
            $referenceId = '';
            $gmailUser = 'me';
            $regex = '/<(.*)>/';
            $email_message = $this->service->users_messages->get($gmailUser, $message->getId());
            $body_message = $this->service->users_messages->get($gmailUser, $message->getId(), $optParams);
            $headers = $email_message->getPayload()->getHeaders();
            $subject = 'Re: '.$this->getHeader($headers, 'Subject');
            $from = $this->getHeader($headers, 'From', $regex); // return [name, email] array of From header
           
            if (strpos($subject, $firstPhrase) !== 'false' && ! empty(strpos($subject, $SecondPhrase)) && ! empty($this->getHeader($headers, 'Message-ID'))) {
                $userCompany = $this->getUserCompanyFromSubject($subject);
                $leadName = trim($this->getLeadNameFromSubject($subject, 'New Message:', 'is requesting a quote'));
                $user = UserService::getUserByCompanyName($userCompany);
                service('Client')->clientHandler($leadName, $user->id, $from['email']);
                $userSettings = SettingService::getSettings($user->id);
                
                $body = $email_message->getPayload()->getBody()->getData(); // base64 encoded body message
                $threadId = $message->getThreadId();
                $thread = $this->service->users_threads->get($gmailUser, $threadId);
                
                if ( $threadId ) {
                    $this->setHeader( 'In-Reply-To', '<'. $from['email'] .'>');
                    $this->setHeader( 'References', '<'. $from['email'] .'>');
                    $this->setHeader( 'Message-ID', $this->getHeader($headers, 'Message-ID'));
                }

                $mailbox = MailboxController::getByEmail($this->fileName);

                $password = $mailbox['password']; // password for current mailbox

                $emailMessage = $this->createMessage($this->fileName, $from['email'], $subject, $userSettings->text);

                $htmlinput = $email_message->getPayload()->getBody()->getData();
                $doc = new DOMDocument();
                $doc->loadHTML(base64_decode($htmlinput));

                $reply_link = '';
                $arr = $doc->getElementsByTagName("a"); // DOMNodeList Object
                foreach($arr as $item) { // DOMElement Object
                    $href =  $item->getAttribute("href");
                    $text = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));
                    if (strcmp($text, "Reply") == 0) {
                        $reply_link = $href;
                        break;
                    }
                }

                $command = escapeshellcmd("python auto_reply.py '".$this->fileName."' '".$password."' '".$reply_link."' '".$userSettings->text."'");
                $response = shell_exec($command);

                // $bodyParams = [
                //     'subject' => $subject,
                //     'from' => $this->fileName,
                //     'to' => $from['email'],
                //     //'cc' => $from['email'],
                //     //'bcc' => $this->fileName,
                //     'body' => $userSettings->text,
                //     'priority' => 1,
                // ];

                //$body = $this->getMessageBody($bodyParams);
                //$body->setThreadId($threadId);
                $this->service->users_messages->send('me', $emailMessage);
                if ($user->plans_id == 'yelp-quoterespond') {
                    SettingService::increaseUsage($user->id);
                    SubscriptionService::createCustomerReport($user);
                }
            }

            // Mark email as Read
            $mods = new \Google_Service_Gmail_ModifyMessageRequest();
            $mods->setRemoveLabelIds(array('UNREAD'));
            $this->service->users_messages->modify($gmailUser, $message->getId(), $mods);
        }
    }

    public function getUserCompanyFromSubject($subject)
	{
		$ini = strpos($subject, 'from');
        $ini += strlen('from ');
        return substr($subject, $ini);
	}

    public function getLeadNameFromSubject($string, $start, $end)
	{
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

    public function getMessageBody($data)
	{
		$body = new \Google_Service_Gmail_Message();

		$this->swiftMessage
			->setSubject($data['subject'])
			->setFrom($data['from'])
			->setTo($data['to'])
			//->setCc($data['cc'])
			//->setBcc($data['bcc'])
			->setBody($data['body'], 'text/html' )
			->setPriority($data['priority']);

		$body->setRaw( $this->base64_encode( $this->swiftMessage->toString() ) );

		return $body;
	}

    public function setHeader( $header, $value )
	{
		$headers = $this->swiftMessage->getHeaders();
		$headers->addTextHeader($header, $value);
	}

    public function base64_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function getHeader($headers, $headerName, $regex = null)
	{
        $value = '';

		foreach ($headers as $header) {
            if ($header->name === $headerName) {
                $value = $header->value;
                if (!is_null($regex)) {
                    preg_match($regex, $value, $matches);

                    $name = preg_replace($regex, '', $value);

                    return [
                        'name'  => $name,
                        'email' => isset( $matches[ 1 ] ) ? $matches[ 1 ] : null,
                    ];
                }
                break;
            }
        }

        return $value;
    }
    
    public function createMessage($sender, $to, $subject, $messageText) {
        $message = new \Google_Service_Gmail_Message();
        
        $rawMessageString = "From: <{$sender}>\r\n";
        $rawMessageString .= "To: <{$to}>\r\n";
        $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: multipart/alternative; charset=utf-8\r\n";
        $rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
        $rawMessageString .= "{$messageText}\r\n";
        
        $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
        $message->setRaw($rawMessage);
        return $message;
    }
    
    static public function delete($email) {
        $path = base_path() . '/storage/app/gmail/tokens/' . $email . '.json';
        unlink($path);
    }
}
