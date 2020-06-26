<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Ddomanskyi\LaravelGmail\Facade\LaravelGmail;
use Ddomanskyi\LaravelGmail\Services\Message\Mail;

use App\Http\Services\MailService;
use App\Http\Services\GmailService;
use App\Http\Repositories\UserRepository;

use QLogger;
use Google_Client;

class GmailController extends Controller
{
    public $client;
    public $service;
    public $path;

    public function __construct() {
        $this->path = base_path();
        $this->client = new Google_Client();
        $this->client->setApplicationName('QuoteRespond');
        $this->client->setScopes([\Google_Service_Gmail::GMAIL_MODIFY, \Google_Service_Gmail::GMAIL_READONLY]);
        $this->client->setAuthConfig($this->path . '/credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        //dd($this->client->revokeToken());
    }

    public function login()
	{
        // Create Auth Url, so user can apply all permissions and receive Auth Code
        return '"' . $this->client->createAuthUrl() . '"';
    }

    public function storeTokenFile(Request $request)
	{
        $data = $request->all();
        $authCode = $data['authCode'];
        $mailboxName = $data['mailboxName'];
        $tokenPath = $this->path . '/storage/app/gmail/tokens/' . $mailboxName . '.json';

        //Exchange authorization code for an access token.
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
        $this->client->setAccessToken($accessToken);

        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0755, true);
        }
        file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));

        return $this->message('Account ' . $mailboxName . '  successfuly added!', 'success');
    }

    public function scanTokenDirectory()
    {
        $tokenDirectoryPath = $this->path . '/storage/app/gmail/tokens/';
        $scan_result = scandir($tokenDirectoryPath, 1);
        $files = [];

        foreach ($scan_result as $result) {
            $result = str_replace('.json', '', $result);
            array_push($files, $result);
        }
        
        return array_diff($files, array('..', '.'));
    }

    public function checkMailboxesByTokenFiles()
    {
        $tokenFiles = $this->scanTokenDirectory();

        foreach ($tokenFiles as $fileName) {
            $gmailService = new GmailService($fileName);
        }
    }
}