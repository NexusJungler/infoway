<?php


namespace App\Service;


use Exception;
use Swift_Message;
use Swift_TransportException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class EmailSenderService
{


	private $mailer;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;


    public function __construct(\Swift_Mailer $mailer, ParameterBagInterface $parameterBag)
	{
		$this->mailer = $mailer;
		$this->parameterBag = $parameterBag;
	}


	/**
	 * Send email
	 *
	 * @param string $to
	 * @param string $subject
	 * @param $body
	 * @param string $bodyContentType
	 * @throws Exception
	 */
	public function sendEmail(string $to, string $subject, $body, string $bodyContentType = 'text/html')
	{
		try
		{

			$message = (new Swift_Message($subject))
				->setFrom($this->parameterBag->get('mailer_user')) // use parameter defined in config/services.yaml
                ->setTo($to)
				->setBody($body, $bodyContentType);

            //dd($this->parameterBag->get('mailer_use'));

            $this->mailer->send($message);

		}
		catch (Swift_TransportException $e)
		{
			throw new Exception($e->getMessage());
		}
		catch (ParameterNotFoundException $e  )
        {
            throw new Exception($e->getMessage());
        }
		catch (Exception $e)
		{
            //dd(preg_match("/\"\w+\"+/", $e->getMessage(), $matches), str_replace('"',null,$matches[0]));
			throw new Exception($e->getMessage());
		}

	}

}