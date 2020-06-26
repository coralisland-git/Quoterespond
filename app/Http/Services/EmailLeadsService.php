<?php

namespace App\Http\Services;

use App\Http\Services\MailService;
use App\Http\Services\UserService;
use App\Http\Services\DialogService;
use App\Http\Services\MagicLinkService;
use App\Http\Services\SendTextService;
use App\Http\Services\ReviewsService;

use App\Http\Repositories\LeadRepository;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\ClientRepository;
use App\Http\Repositories\MatchingRepository;
use App\Http\Repositories\HomeadvisorRepository as HARepository;
use App\Http\Repositories\MatchingRelationRepository;
use Carbon\Carbon;

class EmailLeadsService
{
	const NETWORX_PHRASE = "Here is all the information you need to go and get the job:";
	const NETWORX_PHRASE2 = "Thank you for your recent purchase! Below is a copy of the job details and contact info for your most recent lead. We strongly encourage you to reach out to them immediately.";

	static public function emailHandler()
	{
        $messages = \LaravelGmail::message()->preload()->unread()->all();

		foreach ($messages as $message) {
			if ($message->getFrom()['email'] == 'info@freeenergyevaluation.com') {
				$decoded_body = $message->payload->body->data;
				$body = self::decodeBody($decoded_body);
				$body = trim(strip_tags($body));
				$parse_data = self::parseFreeEnergy($body);
				$lead = $parse_data['lead'];
				$user = UserRepository::getById('1027');
				self::saveLead($lead, $user);
			}

			if (in_array('CATEGORY_UPDATES', $message->load()->labels) || in_array('CATEGORY_PERSONAL', $message->load()->labels)) {
                $body = '';
                $user = '';
                $lead = [];
				$user_name = [];
				if ( ! empty($message->getFrom())) {
					$from = $message->getFrom();
				}
				if ( ! empty($message->getTo())) {
					$to = $message->getTo();
				}

				if ($from['email'] == 'clients@cleanenergyexperts.com') {
					//dd($message->payload->parts[0]->parts[0]->body->data);
					if ( ! empty($message->payload->parts[0]->parts[0]->parts[1])) {
						$decoded_body = $message->payload->parts[0]->parts[0]->parts[1]->body->data;
					} else {
						$decoded_body = $message->payload->parts[0]->parts[0]->body->data;
					}
				} else {
					$decoded_body = $message->payload->body->data;
				}

				if ($from['email'] == 'voicemail@youmail.com') {
					$decoded_body = $message->payload->parts[0]->parts[0]->body->data;
					$body = self::decodeBody($decoded_body);
				}

				if (! $message->hasAttachments()) {
					if ( ! empty($decoded_body)) {
						$body = self::decodeBody($decoded_body);
					} else {
						$body = $message->getPlainTextBody();
					}
				}

				if ($from['email'] == 'notifications@grasshopper.com') {
					$decoded_body = $message->payload->parts[0]->parts[0]->body->data;
					$body = self::decodeBody($decoded_body);
				}

				if ($body) {
					$from = $message->getFrom();
					$to = $message->getTo();
					$subject = $message->getSubject();


					if ($from['email'] == 'success@llmedia.info') {
						$body = trim(strip_tags($body));
						if (empty(strpos($subject, 'HOMELYNK'))) {
							$parse_data = self::parseSunlynk($body);
						} else {
							$parse_data = self::parseHomelynk($body);
						}

						$lead = $parse_data['lead'];
						$user_name = $parse_data['user'];
					}

					if ($from['email'] == 'no-reply@callrail.com') {
						$body = trim(strip_tags($body));
						$parse_data = self::parseCallRail($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = trim(str_replace('Form Submission Alert for', '', $subject));
					}

					//if ($from['email'] == 'customerservice@leads.web.com') {
					if ($from['email'] == 'leadalert@web.com') {
						$body = trim(strip_tags($body));
						$parse_data = self::parseWebCom($body);
						$lead = $parse_data['lead'];
						$user = UserRepository::getById('958');
						$user_name['name_string'] = $user;
					}

					if ( ! empty(strpos($from['email'], 'networx.com')) && (! empty(strpos($body, self::NETWORX_PHRASE)) || ! empty(strpos($body, self::NETWORX_PHRASE2)))) {
						$parse_data = self::parseNetworx($body);
						$lead = $parse_data['lead'];
						$user_name = $parse_data['user'];
					}

					if ( ! empty(strpos($from['email'], 'solarreviews.com')) || ! empty(strpos($body, 'solar-estimate.org'))) {
						$parse_data = self::parseSolarEstimate($body);
						$lead = $parse_data['lead'];
						$user_name = $parse_data['user'];
					}

					if ($from['email'] == 'clients@cleanenergyexperts.com' && strtolower($subject) == 'cee txt') {
						$body = strip_tags($body);
						$parse_data = self::parseCEEAlternative($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = $subject;
					}

                    if ( ! empty(strpos($body, 'cleanenergyexperts'))) {
						$body = strip_tags($body);
						$parse_data = self::parseCEE($body);
						$lead = $parse_data['lead'];
						$user_name = $parse_data['user'];
					}

					if ($from['email'] == 'leads@porch.com') {
						$parse_data = self::parsePorch($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = trim(str_replace('Porch.com lead', '', $subject));
						if (strpos($subject, 'Porch Lead -') !== false) {
							$name_string = explode('-', $subject);
							$user_name['name_string'] = trim($name_string[1]);
							$parse_data = self::parsePorchAlternative($body);
							$lead = $parse_data['lead'];
						}
					}

					if ( ! empty(strpos($from['email'], 'modernizemail.com'))) {
						$parse_data = self::parseModernize($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = str_replace('Modernize ', '', $subject);

						if ($subject == 'Ridge Top CVL') {
							$user_name['name_string'] = 'Ridge Top CVL';
						}
					}

					if ( ! empty(strpos($from['email'], 'mythreebids.com'))) {
						$body = strip_tags($body);
						$parse_data = self::parseMyThreeBids($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = str_replace('Modernize ', '', $subject);
					}

					if ( ! empty(strpos($from['name'], 'quinstreet.com'))) {
						$parse_data = self::parseQuinStreet($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = trim(str_replace(['Quinstreet lead -', 'Quinstreet Lead ?'], '', $subject));
					}

					if ($from['name'] == 'no-reply.ltyzf@zapiermail.com') {
						$user = UserRepository::getUserByForwardingEmail($to[0]['email']);
						$parse_data = self::parseZapier($body, $subject);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = $to[0]['email'];
					}

					if (strtolower($to[0]['email']) == 'closeleadstoday@gmail.com') {
						$user = UserRepository::getUserByForwardingEmail($from['email']);

						if ( ! empty(strpos($body, self::NETWORX_PHRASE))) {
							$parse_data = self::parseNetworx($body);
							$lead = $parse_data['lead'];
							$user_name = $parse_data['user'];
						}
					}

					if (strtolower($to[0]['email']) == 'leads@contractortexter.com') {
						$user = UserRepository::getUserByForwardingEmail($from['email']);
						$parse_data = self::getLeadsFromUserEmail($body);
						$lead = $parse_data['lead'];
						$user_name = $user;
					}

					if (strtolower($to[0]['email']) == 'reviews@contractortexter.com') {
						$user = UserRepository::getUserByForwardingEmail($from['email']);
						$parse_data = self::getLeadForReviews($body);
						$lead = $parse_data['lead'];

						self::handleReviewsLead($user, $lead);
						$message->markAsRead();
						return;
					}

					if ($from['email'] == 'voicemail@youmail.com' &&  strpos($subject, 'Missed Call from') === 0) {
						$parse_data = self::parseYoumail($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = $to[0]['email'];
					}

					if ($from['email'] == 'jb@aquaclearws.com') {
						$user = UserRepository::getById('936');
						$parse_data = self::parsePipedrive($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = $user;
					}

					if (strtolower($from['email']) == 'andrew.coronadosolar@gmail.com') {
						$user = UserRepository::getById('901');
						$parse_data = self::parseCoronado($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = $user;
					}

					if (strtolower($from['email']) == 'leads@conxpros.com') {
						$parse_data = self::parseConxpros($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = $subject;
					}

					if ($from['email'] == 'notifications@grasshopper.com') {
						$user = UserRepository::getById('1046');
						$parse_data = self::parseGrasshopper($body);
						$lead = $parse_data['lead'];
						$user_name['name_string'] = $user;
					}

					if ( ! empty($lead['phone']) && ! empty($user_name)) {
						if (empty($user)) {
							$user = self::checkUser($lead, $user_name);
						}

						if ( ! empty($user)) {
							self::saveLead($lead, $user);
						}
					}
				}
			}

			$message->markAsRead();
		}
	}

	static public function decodeBody($decoded_body) {
		$content = str_replace( '_', '/', str_replace( '-', '+', $decoded_body));
		$body = base64_decode($content);
		return $body;
	}

    static public function checkUser($lead, $user_name)
	{
		$user = '';
		if ( ! empty($lead['phone'])) {
			if ( ! empty($user_name['lastname'])) {
				$user = UserRepository::getUserByFirstAndLastName($user_name['firstname'], $user_name['lastname']);
			}

			if (empty($user) && ! empty($user_name['name_string'])) {
				$matching_relation = MatchingRelationRepository::getRelationByString($user_name['name_string']);
				if ( ! empty($matching_relation)) {
					$user = UserRepository::getById($matching_relation->user_id);
				}
			}
			$matchings = MatchingRepository::getLeadByPhone($lead['phone']);

			if (empty($user) && empty($matchings)) {
				$data = [
					'firstname' => ! empty($lead['firstname']) ? $lead['firstname'] : '',
					'lastname' => ! empty($lead['lastname']) ? $lead['lastname'] : '',
					'phone' => $lead['phone'],
					'company_id' => $lead['company_id'],
					'source' => $lead['source'],
					'name_string' => ! empty($user_name['name_string']) ? $user_name['name_string'] : '',
				];
				$matching = MatchingRepository::create($data);

				$owner = UserRepository::getOwner();
				MailService::sendMatchingAlert($owner, $matching, $user_name['name_string']);
				return false;
			}
			return $user;
		}
    }

	static public function editPhone($phone)
	{

		return trim(str_replace(['-', '(', ')', ' ', '.', '+1'], '', $phone));
    }

    static public function parseSolarEstimate($body)
	{
		$parse_data['lead']['source'] = 'Solar Reviews';
		$parse_data['lead']['company_id'] = 4;
		$parse_data['user']['name_string'] = self::getStringBetween($body, 'To ', ',');

		if ($parse_data['user']['name_string']) {
			$pieces = explode(' ', $parse_data['user']['name_string']);
			$parse_data['user']['firstname'] = $pieces[0];
			$parse_data['user']['lastname'] = $pieces[1];
		}

		$lead_info = trim(self::getStringBetween($body, 'Requested by', 'of site:'));
		//$parse_data['lead']['email'] = ! empty(self::getStringBetween($lead_info, '<mailto:', '>')) ? self::getStringBetween($lead_info, '<mailto:', '>') : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($lead_info, 'Phone:', '</td>')) ? trim(self::getStringBetween($lead_info, 'Phone:', '</td>')) : '';
		$parse_data['lead']['view_phone'] = $parse_data['lead']['phone'];
		$lead_name = ! empty(self::getStringBetween($lead_info, '<td valign="top">', '<br />')) ? trim(self::getStringBetween($lead_info, '<td valign="top">', '<br />')) : '';

		if ($lead_name) {
			$pieces = explode(' ', $lead_name);
			$parse_data['lead']['firstname'] = $pieces[0];
			$parse_data['lead']['lastname'] = $pieces[1];
		}

		return $parse_data;
	}

	static public function parseCEE($body)
	{
		$parse_data['lead']['source'] = 'Clean Energy Experts';
		$parse_data['lead']['company_id'] = 5;
		$parse_data['lead']['email'] = ! empty(self::getStringBetween($body, 'Email', 'Home Address')) ? trim(self::getStringBetween($body, 'Email', 'Home Address')) : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Phone', 'Email')) ? trim(self::getStringBetween($body, 'Phone', 'Email')) : '';
		$parse_data['lead']['firstname'] = ! empty(self::getStringBetween($body, 'First Name', 'Last Name')) ? trim(self::getStringBetween($body, 'First Name', 'Last Name')) : '';
		$parse_data['lead']['lastname'] = ! empty(self::getStringBetween($body, 'Last Name', 'Phone')) ? trim(self::getStringBetween($body, 'Last Name', 'Phone')) : '';
		$parse_data['user']['name_string'] = ! empty(self::getStringBetween($body, 'Dear', ',')) ? trim(self::getStringBetween($body, 'Dear', ',')) : '';

		if ($parse_data['user']['name_string']) {
			$pieces = explode(' ', $parse_data['user']['name_string']);
			$parse_data['user']['firstname'] = $pieces[0];
			$parse_data['user']['lastname'] = $pieces[1];
		}

		return $parse_data;
    }

	static public function parseCEEAlternative($body)
	{
		$parse_data['lead']['source'] = 'Clean Energy Experts';
		$parse_data['lead']['company_id'] = 5;
		$lines_arr = preg_split('/\n|\r/', $body);

		if ( ! empty($lines_arr[0])) {
			$pieces = explode(';', $lines_arr[0]);
			$lead_namestring = $pieces[0];
			$parse_data['lead']['phone'] = trim($pieces[1]);
		}

		if ($lead_namestring) {
			$pieces = explode(' ', $lead_namestring);
			$parse_data['lead']['firstname'] = $pieces[0];
			$parse_data['lead']['lastname'] = $pieces[1];
		}

		return $parse_data;
    }

	static public function parseNetworx($body)
	{
		$parse_data['lead']['source'] = 'Networx';
		$parse_data['lead']['company_id'] = 6;
		if (! empty(strpos($body, self::NETWORX_PHRASE2))) {
			$parse_data['user']['name_string'] = ! empty(self::getStringBetween($body, 'Hi', 'Thank')) ? trim(self::getStringBetween($body, 'Hi', 'Thank')) : '';
		} else {
			$parse_data['user']['name_string'] = ! empty(self::getStringBetween($body, 'Hi', ',')) ? trim(self::getStringBetween($body, 'Hi', ',')) : '';
		}
		$parse_data['lead']['email'] = '';
		$phonePos = strpos($body, 'Phone:');

        if ($phonePos !== false) {
			$phone_string = substr($body, $phonePos, 22);
			$phone = substr($phone_string, 8, 14);
			$parse_data['lead']['phone'] = self::editPhone($phone);
		}

		$lead_name = ! empty(self::getStringBetween($body, 'Name:', 'Phone')) ? trim(self::getStringBetween($body, 'Name:', 'Phone')) : '';

		if ($lead_name) {
			$pieces = explode(' ', $lead_name);
			$parse_data['lead']['firstname'] = $pieces[0];

			if ( ! empty($pieces[1])) {
				$parse_data['lead']['lastname'] = $pieces[1];
			}
		}

		if ($parse_data['user']['name_string']) {
			$pieces = explode(' ', $parse_data['user']['name_string']);
			$parse_data['user']['firstname'] = $pieces[0];

			if ( ! empty($pieces[1])) {
				$parse_data['user']['lastname'] = $pieces[1];
			}
		}

		return $parse_data;
	}

	static public function parsePorch($body)
	{
		$parse_data['lead']['source'] = 'Porch';
		$parse_data['lead']['company_id'] = 3;
		$parse_data['lead']['firstname'] = ! empty(self::getStringBetween($body, 'First Name:', 'Last Name:')) ? trim(strip_tags(self::getStringBetween($body, 'First Name:', 'Last Name:'))) : '';
		$parse_data['lead']['lastname'] = ! empty(self::getStringBetween($body, 'Last Name:', 'Email:')) ? trim(strip_tags(self::getStringBetween($body, 'Last Name:', 'Email:'))) : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Phone:', 'Address:')) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Phone:', 'Address:')))) : '';

		return $parse_data;
    }

	static public function parsePorchAlternative($body)
	{
		$parse_data['lead']['source'] = 'Porch';
		$parse_data['lead']['company_id'] = 3;
		$parse_data['lead']['firstname'] = ! empty(self::getStringBetween($body, 'First Name:', 'Last Name:')) ? trim(strip_tags(self::getStringBetween($body, 'First Name:', 'Last Name:'))) : '';
		$parse_data['lead']['lastname'] = ! empty(self::getStringBetween($body, 'Last Name:', 'Address:')) ? trim(strip_tags(self::getStringBetween($body, 'Last Name:', 'Address:'))) : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Phone:', 'Service Type:')) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Phone:', 'Service Type:')))) : '';

		return $parse_data;
    }

	static public function parseModernize($body)
	{
		$parse_data['lead']['source'] = 'Modernize';
		$parse_data['lead']['company_id'] = 8;
		$parse_data['lead']['firstname'] = ! empty(self::getStringBetween($body, 'First Name:', 'Last Name:')) ? trim(self::getStringBetween($body, 'First Name:', 'Last Name:')) : '';
		$parse_data['lead']['lastname'] = ! empty(self::getStringBetween($body, 'Last Name:', 'Address:')) ? trim(self::getStringBetween($body, 'Last Name:', 'Address:')) : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Home Phone:', 'Project:')) ? self::editPhone(trim(self::getStringBetween($body, 'Home Phone:', 'Project:'))) : '';

		return $parse_data;
    }

	static public function parseZapier($body, $subject)
	{
		$parse_data = [];
		$parse_data['lead']['source'] = 'User Email';
		$parse_data['lead']['company_id'] = 99;

		if (strpos($subject, 'Facebook')) {
			$parse_data['lead']['source'] = 'Facebook';
			$parse_data['lead']['company_id'] = 9;

			if (strpos($body, 'Phone:')) {
				$parse_data['lead']['phone'] = self::editPhone(trim(self::getStringBetween($body, 'Phone:', PHP_EOL)));
			}
		}

		if (strpos($body, 'Name:')) {
			$lead_name_string = ! empty(self::getStringBetween($body, 'Name:', 'Email:')) ? trim(self::getStringBetween($body, 'Name:', 'Email:')) : '';
			$lead_name = self::splitString($lead_name_string);
			$parse_data['lead']['firstname'] = $lead_name[0];
			$parse_data['lead']['lastname'] = ! empty($lead_name[1]) ? $lead_name[1] : '';
			$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Phone:', 'Address:')) ? self::editPhone(trim(self::getStringBetween($body, 'Phone:', 'Address:'))) : '';
		} else {
			$lines_arr = preg_split('/\n|\r/', $body);
			if ( ! empty($lines_arr[0])) {
				$lead_name = self::splitString($lines_arr[0]);
				$parse_data['lead']['firstname'] = ! empty($lead_name[0]) ? $lead_name[0] : '';
				$parse_data['lead']['lastname'] = ! empty($lead_name[1]) ? $lead_name[1] : '';
				$parse_data['lead']['phone'] = ! empty($lines_arr[2]) ? self::editPhone(trim($lines_arr[2])) : '';
			}
		}

		return $parse_data;
	}

	static public function parseMyThreeBids($body)
	{
		$parse_data['lead']['source'] = 'My Three Bids';
		$parse_data['lead']['company_id'] = 10;
		$parse_data['lead']['firstname'] = ! empty(self::getStringBetween($body, 'First Name:', 'Last Name:')) ? trim(self::getStringBetween($body, 'First Name:', 'Last Name:')) : '';
		$parse_data['lead']['lastname'] = ! empty(self::getStringBetween($body, 'Last Name:', 'Address 1:')) ? trim(self::getStringBetween($body, 'Last Name:', 'Address 1:')) : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Phone:', 'Email:')) ? self::editPhone(trim(self::getStringBetween($body, 'Phone:', 'Email:'))) : '';

		return $parse_data;
	}

	static public function parseQuinStreet($body)
	{
		$parse_data['lead']['source'] = 'QuinStreet';
		$parse_data['lead']['company_id'] = 13;
		$parse_data['lead']['firstname'] = ! empty(self::getStringBetween($body, 'FirstName:', 'PrimaryNumber:')) ? trim(self::getStringBetween($body, 'FirstName:', 'PrimaryNumber:')) : '';
		$parse_data['lead']['lastname'] = ! empty(self::getStringBetween($body, 'LastName:', 'WorkPhone:')) ? trim(self::getStringBetween($body, 'LastName:', 'WorkPhone:')) : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'PrimaryNumber:', 'Product:')) ? self::editPhone(trim(self::getStringBetween($body, 'PrimaryNumber:', 'Product:'))) : '';

		return $parse_data;
	}

	static public function parseYoumail($body)
	{
		$parse_data['lead']['source'] = 'Youmail';
		$parse_data['lead']['company_id'] = 12;
		$parse_data['lead']['name_string'] = (! empty(self::getStringBetween($body, 'From:', ' at')) && trim(strlen(self::getStringBetween($body, 'From:', ' at'))) <= 30) ? trim(strip_tags(self::getStringBetween($body, 'From:', ' at'))) : '';
		$parse_data['lead']['phone'] = (! empty(self::getStringBetween($body, ' at ', 'To:')) && trim(strlen(self::getStringBetween($body, ' at ', 'To:'))) <= 20) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, ' at ', 'To:')))) : '';

		if ($parse_data['lead']['name_string']) {
			$pieces = explode(' ', $parse_data['lead']['name_string']);
			$parse_data['lead']['firstname'] = $pieces[0];

			if ( ! empty($pieces[1])) {
				$parse_data['lead']['lastname'] = $pieces[1];
			}
		}

		return $parse_data;
    }

	static public function parseCoronado($body)
	{
		$parse_data['lead']['source'] = 'Coronado';
		$parse_data['lead']['company_id'] = 98;
		$parse_data['lead']['name_string'] = ! empty(self::getStringBetween($body, 'Name :', 'Phone:')) ? trim(strip_tags(self::getStringBetween($body, 'Name :', 'Phone:'))) : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Phone:', PHP_EOL)) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Phone:', PHP_EOL)))) : '';

		if ($parse_data['lead']['name_string']) {
			$pieces = explode(' ', $parse_data['lead']['name_string']);
			$parse_data['lead']['firstname'] = $pieces[0];

			if ( ! empty($pieces[1])) {
				$parse_data['lead']['lastname'] = $pieces[1];
			}
		}

		return $parse_data;
    }

	static public function parseConxpros($body)
	{
		$parse_data['lead']['source'] = 'ConXpros';
		$parse_data['lead']['company_id'] = 14;
		$parse_data['lead']['firstname'] = ! empty(self::getStringBetween($body, 'First Name:', PHP_EOL)) ? trim(strip_tags(self::getStringBetween($body, 'First Name:', PHP_EOL))) : '';
		$parse_data['lead']['lastname'] = ! empty(self::getStringBetween($body, 'Last Name:', PHP_EOL)) ? trim(strip_tags(self::getStringBetween($body, 'Last Name:', PHP_EOL))) : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Primary Phone:', PHP_EOL)) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Primary Phone:', PHP_EOL)))) : '';

		return $parse_data;
    }

	static public function parsePipedrive($body)
	{
		$parse_data['lead']['source'] = 'Pipedrive';
		$parse_data['lead']['company_id'] = 101;
		$parse_data['lead']['name_string'] = ! empty(self::getStringBetween($body, 'Contact Name:', PHP_EOL)) ? trim(strip_tags(self::getStringBetween($body, 'Contact Name:', PHP_EOL))) : '';
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Phone:', PHP_EOL)) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Phone:', PHP_EOL)))) : '';

		if ($parse_data['lead']['name_string']) {
			$pieces = explode(' ', $parse_data['lead']['name_string']);
			$parse_data['lead']['firstname'] = $pieces[0];

			if ( ! empty($pieces[1])) {
				$parse_data['lead']['lastname'] = $pieces[1];
			}
		}

		return $parse_data;
    }

	static public function parseWebCom($body)
	{
		$parse_data['lead']['source'] = 'Web.com';
		$parse_data['lead']['company_id'] = 107;
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Customer Phone:', 'Customer Location:')) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Customer Phone:', 'Customer Location:')))) : '';
		$parse_data['lead']['firstname'] = 'Hi';
		$parse_data['lead']['lastname'] = '';

		return $parse_data;
    }

	static public function parseCallRail($body)
	{
		$parse_data['lead']['source'] = 'Call Rail';
		$parse_data['lead']['company_id'] = 108;
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Form Fields 0c4e42d:', PHP_EOL)) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Form Fields 0c4e42d:', PHP_EOL)))) : '';
		$parse_data['lead']['name_string'] = ! empty(self::getStringBetween($body, 'Form Fields Name:', PHP_EOL)) ? trim(strip_tags(self::getStringBetween($body, 'Form Fields Name:', PHP_EOL))) : '';

		if ($parse_data['lead']['name_string']) {
			$pieces = explode(' ', $parse_data['lead']['name_string']);
			$parse_data['lead']['firstname'] = $pieces[0];

			if ( ! empty($pieces[1])) {
				$parse_data['lead']['lastname'] = $pieces[1];
			}
		}

		return $parse_data;
    }

	static public function parseSunlynk($body)
	{
		$parse_data['lead']['source'] = 'Sunlynk';
		$parse_data['lead']['company_id'] = 15;
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Phone:', PHP_EOL)) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Phone:', PHP_EOL)))) : '';
		$parse_data['lead']['firstname'] = ! empty(self::getStringBetween($body, 'First Name:', PHP_EOL)) ? trim(strip_tags(self::getStringBetween($body, 'First Name:', PHP_EOL))) : '';
		$parse_data['lead']['lastname'] = ! empty(self::getStringBetween($body, 'Last Name:', PHP_EOL)) ? trim(strip_tags(self::getStringBetween($body, 'Last Name:', PHP_EOL))) : '';
		$parse_data['user']['name_string'] = ! empty(self::getStringBetween($body, 'Hello ', ',')) ? trim(strip_tags(self::getStringBetween($body, 'Hello ', ','))) : '';

		return $parse_data;
    }

	static public function parseHomelynk($body)
	{
		$parse_data['lead']['source'] = 'Homelynk';
		$parse_data['lead']['company_id'] = 16;
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Phone:', PHP_EOL)) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Phone:', PHP_EOL)))) : '';
		$parse_data['lead']['name_string'] = ! empty(self::getStringBetween($body, 'Homeowner Name:', PHP_EOL)) ? trim(strip_tags(self::getStringBetween($body, 'Homeowner Name:', PHP_EOL))) : '';

		if ($parse_data['lead']['name_string']) {
			$pieces = explode(' ', $parse_data['lead']['name_string']);
			$parse_data['lead']['firstname'] = $pieces[0];

			if ( ! empty($pieces[1])) {
				$parse_data['lead']['lastname'] = $pieces[1];
			}
		}
		$parse_data['user']['name_string'] = ! empty(self::getStringBetween($body, 'Hello ', ',')) ? trim(strip_tags(self::getStringBetween($body, 'Hello ', ','))) : '';


		return $parse_data;
    }

	static public function parseFreeEnergy($body)
	{
		$parse_data['lead']['source'] = 'Free Energy';
		$parse_data['lead']['company_id'] = 111;
		$phone = ! empty(self::getStringBetween($body, 'Phone', 'Email')) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Phone', 'Email')))) : '';
		$parse_data['lead']['phone'] = trim(str_replace('&nbsp;', '', $phone));
		$name_string = ! empty(self::getStringBetween($body, 'Name', 'Address')) ? trim(self::getStringBetween($body, 'Name', 'Address')) : '';
		$name_string = trim(str_replace('&nbsp;', '', $name_string));

		if ($name_string) {
			$pieces = explode(' ', $name_string);
			$parse_data['lead']['firstname'] = $pieces[0];

			if ( ! empty($pieces[1])) {
				$parse_data['lead']['lastname'] = $pieces[1];
			}
		}

		return $parse_data;
    }

	static public function parseGrasshopper($body)
	{
		$parse_data['lead']['source'] = 'Grasshopper';
		$parse_data['lead']['company_id'] = 112;
		$parse_data['lead']['phone'] = ! empty(self::getStringBetween($body, 'Caller:', PHP_EOL)) ? strip_tags(self::editPhone(trim(self::getStringBetween($body, 'Caller:', PHP_EOL)))) : '';
		$parse_data['lead']['firstname'] = 'Hi';
		$parse_data['lead']['lastname'] = '';

		return $parse_data;
    }

	static public function getLeadsFromUserEmail($body) {
		$lines_arr = preg_split('/\n|\r/', $body);

		if ( ! empty($lines_arr[0])) {
			$lead_name = self::splitString($lines_arr[0]);
			$parse_data['lead']['firstname'] = ! empty($lead_name[0]) ? $lead_name[0] : '';
			$parse_data['lead']['lastname'] = ! empty($lead_name[1]) ? $lead_name[1] : '';
			$parse_data['lead']['phone'] = ! empty($lines_arr[2]) ? self::editPhone(trim($lines_arr[2])) : '';
			$parse_data['lead']['source'] = 'User Email';
			$parse_data['lead']['company_id'] = 99;
		}

		return $parse_data;
	}

	static public function getLeadForReviews($body) {
		$lines_arr = preg_split('/\n|\r/', $body);

		if ( ! empty($lines_arr[0])) {
			$lead_name = self::splitString($lines_arr[0]);
			$parse_data['lead']['firstname'] = ! empty($lead_name[0]) ? $lead_name[0] : '';
			$parse_data['lead']['lastname'] = ! empty($lead_name[1]) ? $lead_name[1] : '';

			foreach ($lines_arr as $key => $line) {
				if (! empty($line[$key]) && $key != 0) {
					$parse_data['lead']['phone'] = ! empty($lines_arr[$key]) ? self::editPhone(trim($lines_arr[$key])) : '';
					break;
				}
			}
			$parse_data['lead']['source'] = 'For Reviews';
			$parse_data['lead']['company_id'] = 100;
		}

		return $parse_data;
	}

    static public function getStringBetween($string, $start, $end)
	{
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

	static public function splitString($string)
	{
		return explode(' ', $string);
    }

	static public function handleReviewsLead($user, $lead)
	{
		$backup_data = [
			'user_id' => $user->id,
			'team_id' => $user->teams_id,
			'code' => false,
			'data' => json_encode($lead),
			'exists' => false,
		];

		$backup = LeadRepository::create($backup_data);

		$data = [
            'phone' => $lead['phone'],
            'firstName' => ! empty($lead['firstname']) ? $lead['firstname'] : '',
            'lastName' => ! empty($lead['lastname']) ? $lead['lastname'] : '',
            'email' => ! empty($lead['email']) ? $lead['email'] : '',
            'user' => $user,
            'lead_id' => $backup->id,
        ];

		$client = ClientService::clientHandler($data, $lead['source']);

		if (! empty($client)) {
			ReviewsService::handleReviewsLead($client, $user);
		}
    }

    static public function saveLead($lead, $user)
	{
        $backup_data = [
			'user_id' => $user->id,
			'team_id' => $user->teams_id,
			'code' => false,
			'data' => json_encode($lead),
			'exists' => false,
		];
        $backup = LeadRepository::create($backup_data);

        $data = [
            'phone' => $lead['phone'],
            'firstName' => ! empty($lead['firstname']) ? $lead['firstname'] : '',
            'lastName' => ! empty($lead['lastname']) ? $lead['lastname'] : '',
            'email' => ! empty($lead['email']) ? $lead['email'] : '',
            'user' => $user,
            'lead_id' => $backup->id,
        ];

        $client = ClientService::clientHandler($data, $lead['source']);

        if ( ! empty($client)) {
            $settings = HARepository::getUserSettings($user->id);
            $values = [
                'first_name' => $client->firstname,
                'last_name' => $client->lastname,
                'link' => '',
                'jobs_pics' => SendTextService::jobsPicsGenerate($settings->text, $user->id),
            ];

			$magic_text = MagicLinkService::getTextWithClickLink($settings->text, $client->id);
			if ( ! empty($magic_text)) {
				$text = SendTextService::replaceTextTags($magic_text, $values, $user->company_name);
				DialogService::createDialog($user, $client, $text);
			}
        }
	}
}
