<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use JMS\Serializer\SerializerInterface;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Request\AuthenticateMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\ChangeISDSPassword;
use TomasKulhanek\CzechDataBox\DTO\Request\CheckDataBox;
use TomasKulhanek\CzechDataBox\DTO\Request\ConfirmDelivery;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\DataBoxCreditInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\DTInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\FindDataBox;
use TomasKulhanek\CzechDataBox\DTO\Request\FindPersonalDataBox;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxActivityStatus;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxList;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfReceivedMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfSentMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\GetMessageStateChanges;
use TomasKulhanek\CzechDataBox\DTO\Request\GetSignedDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\IRequest;
use TomasKulhanek\CzechDataBox\DTO\Request\ISDSSearch3;
use TomasKulhanek\CzechDataBox\DTO\Request\MarkMessageAsDownloaded;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\PDZInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\PDZSendInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\ResignISDSDocument;
use TomasKulhanek\CzechDataBox\DTO\Request\SignedMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\SignedSentMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\VerifyMessage;
use TomasKulhanek\CzechDataBox\DTO\Response\GetOwnerInfoFromLogin;
use TomasKulhanek\CzechDataBox\DTO\Response\GetPasswordInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\GetUserInfoFromLogin;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\FileSizeOverflow;
use TomasKulhanek\CzechDataBox\Exception\MissingMainFile;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\RecipientCountOverflow;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Utils\BinarySuffix;

class Connector
{
	private readonly ClientProviderInterface $provider;

	private readonly SerializerInterface $serializer;

	public function __construct(SerializerInterface $serializer, ClientProviderInterface $provider)
	{
		$this->provider = $provider;
		$this->serializer = $serializer;
	}

	public function findDataBox(Account $account, FindDataBox $input): DTO\Response\FindDataBox
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\FindDataBox::class);
	}

	public function pdzInfo(Account $account, PDZInfo $input): DTO\Response\PDZInfo
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\PDZInfo::class);
	}

	public function dataBoxCreditInfo(Account $account, DataBoxCreditInfo $input): DTO\Response\DataBoxCreditInfo
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\DataBoxCreditInfo::class);
	}

	public function isdsSearch3(Account $account, ISDSSearch3 $input): DTO\Response\ISDSSearch3
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\ISDSSearch3::class);
	}

	public function getDataBoxActivityStatus(Account $account, GetDataBoxActivityStatus $input): DTO\Response\GetDataBoxActivityStatus
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\GetDataBoxActivityStatus::class);
	}

	public function dtInfo(Account $account, DTInfo $input): DTO\Response\DTInfo
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\DTInfo::class);
	}

	public function pdzSendInfo(Account $account, PDZSendInfo $input): DTO\Response\PDZSendInfo
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\PDZSendInfo::class);
	}

	public function findPersonalDataBox(Account $account, FindPersonalDataBox $input): DTO\Response\FindPersonalDataBox
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\FindPersonalDataBox::class);
	}

	public function getDataBoxList(Account $account, GetDataBoxList $input): DTO\Response\GetDataBoxList
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\GetDataBoxList::class);
	}

	public function checkDataBox(Account $account, CheckDataBox $input): DTO\Response\CheckDataBox
	{
		return $this->send($account, EndpointProvider::SEARCH, $input, DTO\Response\CheckDataBox::class);
	}

	public function getOwnerInfoFromLogin(Account $account): GetOwnerInfoFromLogin
	{
		return $this->send($account, EndpointProvider::ACCESS, (new DTO\Request\GetOwnerInfoFromLogin()), GetOwnerInfoFromLogin::class);
	}

	public function changeIsdsPassword(Account $account, ChangeISDSPassword $input): DTO\Response\ChangeISDSPassword
	{
		return $this->send($account, EndpointProvider::ACCESS, $input, DTO\Response\ChangeISDSPassword::class);
	}

	public function getPasswordExpirationInfo(Account $account): GetPasswordInfo
	{
		return $this->send($account, EndpointProvider::ACCESS, (new DTO\Request\GetPasswordInfo()), GetPasswordInfo::class);
	}

	public function authenticateMessage(Account $account, AuthenticateMessage $input): DTO\Response\AuthenticateMessage
	{
		return $this->send($account, EndpointProvider::OPERATIONS, $input, DTO\Response\AuthenticateMessage::class);
	}
	
	public function getUserInfoFromLogin(Account $account): GetUserInfoFromLogin
	{
		return $this->send($account, EndpointProvider::ACCESS, (new DTO\Request\GetUserInfoFromLogin()), GetUserInfoFromLogin::class);
	}

	/**
	 * @deprecated
	 */
	public function verifyMessage(Account $account, VerifyMessage $input): DTO\Response\VerifyMessage
	{
		return $this->send($account, EndpointProvider::INFO, $input, DTO\Response\VerifyMessage::class);
	}

	public function createMessage(Account $account, CreateMessage $input): DTO\Response\CreateMessage
	{
		$recipientsCount = count($input->getRecipients());
		if ($recipientsCount < 1) {
			throw new MissingRequiredField('recipient');
		}
		if ($recipientsCount > 50) {
			throw new RecipientCountOverflow(sprintf('More than 50 recipients are assigned. Currently, %d are added.', $recipientsCount));
		}
		$sumFileSize = 0;
		/** @var DTO\File $file */
		foreach ($input->getFiles() as $file) {
			if ($file->getEncodedContent() instanceof \SplFileInfo) {
				$sumFileSize += $file->getEncodedContent()->getSize();
			}
		}
		if ($sumFileSize > 26_214_400) {
			throw new FileSizeOverflow(sprintf('Maximum size of all files can be maximal 25MB. Current size is %s.', BinarySuffix::convert($sumFileSize)));
		}
		if (!$input->getMainFile() instanceof File) {
			throw new MissingMainFile('The message can\'t be send without main attachment');
		}
		if (empty($input->getEnvelope()->getAnnotation())) {
			throw new MissingRequiredField('annotation');
		}
		return $this->send($account, EndpointProvider::OPERATIONS, $input, DTO\Response\CreateMessage::class);
	}

	public function messageDownload(Account $account, MessageDownload $input): DTO\Response\MessageDownload
	{
		return $this->send($account, EndpointProvider::OPERATIONS, $input, DTO\Response\MessageDownload::class);
	}

	public function signedMessageDownload(Account $account, SignedMessageDownload $input): DTO\Response\SignedMessageDownload
	{
		return $this->send($account, EndpointProvider::OPERATIONS, $input, DTO\Response\SignedMessageDownload::class);
	}

	public function signedSentMessageDownload(Account $account, SignedSentMessageDownload $input): DTO\Response\SignedSentMessageDownload
	{
		return $this->send($account, EndpointProvider::OPERATIONS, $input, DTO\Response\SignedSentMessageDownload::class);
	}

	public function resignIsdsDocument(Account $account, ResignISDSDocument $input): DTO\Response\ResignISDSDocument
	{
		return $this->send($account, EndpointProvider::OPERATIONS, $input, DTO\Response\ResignISDSDocument::class);
	}

	public function messageEnvelopeDownload(Account $account, MessageEnvelopeDownload $input): DTO\Response\MessageEnvelopeDownload
	{
		return $this->send($account, EndpointProvider::INFO, $input, DTO\Response\MessageEnvelopeDownload::class);
	}

	public function markMessageAsDownloaded(Account $account, MarkMessageAsDownloaded $input): DTO\Response\MarkMessageAsDownloaded
	{
		return $this->send($account, EndpointProvider::INFO, $input, DTO\Response\MarkMessageAsDownloaded::class);
	}

	public function getDeliveryInfo(Account $account, GetDeliveryInfo $input): DTO\Response\GetDeliveryInfo
	{
		return $this->send($account, EndpointProvider::INFO, $input, DTO\Response\GetDeliveryInfo::class);
	}

	public function getSignedDeliveryInfo(Account $account, GetSignedDeliveryInfo $input): DTO\Response\GetSignedDeliveryInfo
	{
		return $this->send($account, EndpointProvider::INFO, $input, DTO\Response\GetSignedDeliveryInfo::class);
	}

	public function getListOfSentMessages(Account $account, GetListOfSentMessages $input): DTO\Response\GetListOfSentMessages
	{
		return $this->send($account, EndpointProvider::INFO, $input, DTO\Response\GetListOfSentMessages::class);
	}

	public function getListOfReceivedMessages(Account $account, GetListOfReceivedMessages $input): DTO\Response\GetListOfReceivedMessages
	{
		return $this->send($account, EndpointProvider::INFO, $input, DTO\Response\GetListOfReceivedMessages::class);
	}

	public function getMessageStateChanges(Account $account, GetMessageStateChanges $input): DTO\Response\GetMessageStateChanges
	{
		return $this->send($account, EndpointProvider::INFO, $input, DTO\Response\GetMessageStateChanges::class);
	}

	/**
	 * @deprecated
	 */
	public function confirmDelivery(Account $account, ConfirmDelivery $input): DTO\Response\ConfirmDelivery
	{
		return $this->send($account, EndpointProvider::INFO, $input, DTO\Response\ConfirmDelivery::class);
	}

	private function getXmlDocument(?string $xmlContent = null): DOMDocument
	{
		$document = new DOMDocument('1.0', 'UTF-8');
		if ($xmlContent !== null) {
			$document->loadXML($xmlContent);
			return $document;
		}
		$document->loadXML('<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Header/><SOAP-ENV:Body></SOAP-ENV:Body></SOAP-ENV:Envelope>');
		return $document;
	}

	private function getValueByXpath(DOMDocument $document, string $xpath): ?string
	{
		$domXpath = new DOMXPath($document);
		$result = null;
		$res = $domXpath->evaluate($xpath);
		if ($res instanceof DOMNodeList) {
			foreach ($res as $node) {
				if ($node instanceof DOMElement || $node instanceof DOMDocument) {
					$nodeValue = null;
					$children = $node->childNodes;
					foreach ($children as $child) {
						$nodeValue .= $document->saveXML($child);
					}
				} else {
					$nodeValue = $node->nodeValue;
				}
				$result .= $nodeValue;
			}
		}
		return $result;
	}

	/**
	 * @template T of DTO\Response\IResponse
	 * @return IResponse
	 * @phpstan-param class-string<T> $responseClass
	 * @phpstan-return T
	 * @throws Exception\ConnectionException
	 */
	protected function send(Account $account, int $serviceType, IRequest $request, string $responseClass): IResponse
	{
		if (!is_subclass_of($responseClass, IResponse::class)) {
			throw new ConnectionException();
		}

		$request = $this->serializer->serialize($request, 'xml');
		$request = $this->getXmlDocument($request);

		$requestDocument = $this->getXmlDocument();
		$requestDocumentXpath = new DOMXPath($requestDocument);
		if (empty($requestDocument->documentElement)) {
			throw new ConnectionException();
		}
		$bodyNode = $requestDocumentXpath->evaluate('//' . $requestDocument->documentElement->prefix . ':Body');
		$new = $bodyNode[0]->ownerDocument->importNode($request->documentElement, true);
		if ($bodyNode[0]->nextSibling) {
			$bodyNode[0]->insertBefore($new, $bodyNode[0]->nextSibling);
		} else {
			$bodyNode[0]->appendChild($new);
		}
		$xmlBody = $requestDocument->saveXml();
		if (!$xmlBody) {
			throw new ConnectionException();
		}

		$response = $this->provider->sendRequest($account, $serviceType, $xmlBody);
		$soapResponse = $this->getXmlDocument($response);
		if (empty($soapResponse->documentElement)) {
			throw new ConnectionException('The response is empty');
		}
		$response = $this->getValueByXpath($soapResponse, '//' . $soapResponse->documentElement->prefix . ':Body');
		$soapResponse = null;
		$dom = $this->getXmlDocument($response);
		if (empty($dom->documentElement)) {
			throw new ConnectionException('The response is empty');
		}
		$prefix = $dom->documentElement->prefix;
		if ($prefix !== 'p') {
			$dom->documentElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:p', 'http://isds.czechpoint.cz/v20');
			/** @var string $response */
			$response = $dom->saveXML();
			$regex = ['/(<|<\/)' . $prefix . ':(\w*)(\s|>|\/>)/'];
			$replace = ['\1p:\2\3'];
			$response = preg_replace($regex, $replace, $response);
		}
		if (empty($response)) {
			throw new ConnectionException('The response is empty');
		}
		return $this->serializer->deserialize($response, $responseClass, 'xml');
	}
}
