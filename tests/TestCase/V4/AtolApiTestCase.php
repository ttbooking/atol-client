<?php

declare(strict_types=1);

namespace Lamoda\AtolClient\Tests\TestCase\V4;

use Lamoda\AtolClient\V4\AtolApi;
use Lamoda\AtolClient\V4\DTO\GetToken\GetTokenRequest;
use Lamoda\AtolClient\V4\DTO\Register\AgentInfo;
use Lamoda\AtolClient\V4\DTO\Register\AgentType;
use Lamoda\AtolClient\V4\DTO\Register\Client as ClientDto;
use Lamoda\AtolClient\V4\DTO\Register\Company;
use Lamoda\AtolClient\V4\DTO\Register\Item;
use Lamoda\AtolClient\V4\DTO\Register\PayingAgent;
use Lamoda\AtolClient\V4\DTO\Register\Payment;
use Lamoda\AtolClient\V4\DTO\Register\PaymentMethod;
use Lamoda\AtolClient\V4\DTO\Register\PaymentObject;
use Lamoda\AtolClient\V4\DTO\Register\PaymentType;
use Lamoda\AtolClient\V4\DTO\Register\Receipt;
use Lamoda\AtolClient\V4\DTO\Register\RegisterRequest;
use Lamoda\AtolClient\V4\DTO\Register\Status as RegisterStatus;
use Lamoda\AtolClient\V4\DTO\Register\SupplierInfo;
use Lamoda\AtolClient\V4\DTO\Register\Vat;
use Lamoda\AtolClient\V4\DTO\Register\VatType;
use Lamoda\AtolClient\V4\DTO\Report\Status;
use Lamoda\AtolClient\V4\DTO\Shared\ErrorType;
use PHPUnit\Framework\TestCase;

abstract class AtolApiTestCase extends TestCase
{
    /**
     * @var AtolApi
     */
    private $api;

    abstract protected function createApi(): AtolApi;

    abstract protected function getLogin(): string;

    abstract protected function getPassword(): string;

    abstract protected function getGroupCode(): string;

    abstract protected function setUpTestGetToken(): void;

    abstract protected function setUpTestGetTokenWithInvalidCredentials(): void;

    abstract protected function setUpTestSell(): void;

    abstract protected function setUpTestSellWithInvalidRequest(): void;

    abstract protected function setUpTestSellRefund(): void;

    abstract protected function setUpTestSellRefundWithInvalidRequest(): void;

    abstract protected function setUpTestReport(): void;

    protected function setUp(): void
    {
        parent::setUp();

        $this->api = $this->createApi();
    }

    final public function testGetToken(): void
    {
        $this->setUpTestGetToken();

        $request = new GetTokenRequest(
            $this->getLogin(),
            $this->getPassword()
        );

        $response = $this->api->getToken($request);

        $this->assertNotNull($response->getToken());
        $this->assertNull($response->getError());
    }

    final public function testGetTokenWithInvalidCredentials(): void
    {
        $this->setUpTestGetTokenWithInvalidCredentials();

        $login = 'invalid';
        $password = 'invalid';

        $request = new GetTokenRequest(
            $login,
            $password
        );

        $response = $this->api->getToken($request);

        $this->assertNull($response->getToken());
        $this->assertNotNull($response->getError());

        $error = $response->getError();

        $this->assertEquals(ErrorType::SYSTEM(), $error->getType());
        $this->assertEquals(12, $error->getCode());
        $this->assertEquals('Неверный логин или пароль', $error->getText());
    }

    final public function testSell(): void
    {
        $this->setUpTestSell();

        $token = $this->requestToken();

        $request = $this->createRegisterRequest();

        $response = $this->api->sell($this->getGroupCode(), $token, $request);

        $this->assertNull($response->getError());
        $this->assertNotNull($response->getUuid());
        $this->assertInstanceOf(\DateTimeInterface::class, $response->getTimestamp());
        $this->assertEquals(RegisterStatus::WAIT(), $response->getStatus());
    }

    final public function testSellWithInvalidRequest(): void
    {
        $this->setUpTestSellWithInvalidRequest();

        $token = $this->requestToken();

        $request = $this->createInvalidRegisterRequest();

        $response = $this->api->sell($this->getGroupCode(), $token, $request);

        $this->assertNotNull($response->getError());
        $this->assertNull($response->getUuid());
        $this->assertInstanceOf(\DateTimeInterface::class, $response->getTimestamp());
        $this->assertEquals(RegisterStatus::FAIL(), $response->getStatus());

        $error = $response->getError();

        $this->assertEquals(32, $error->getCode());
    }

    final public function testSellRefund(): void
    {
        $this->setUpTestSellRefund();

        $token = $this->requestToken();

        $request = $this->createRegisterRequest();

        $response = $this->api->sellRefund($this->getGroupCode(), $token, $request);

        $this->assertNull($response->getError());
        $this->assertNotNull($response->getUuid());
        $this->assertInstanceOf(\DateTimeInterface::class, $response->getTimestamp());
    }

    final public function testSellRefundWithInvalidRequest(): void
    {
        $this->setUpTestSellRefundWithInvalidRequest();

        $token = $this->requestToken();

        $request = $this->createInvalidRegisterRequest();

        $response = $this->api->sellRefund($this->getGroupCode(), $token, $request);

        $this->assertNotNull($response->getError());
        $this->assertNull($response->getUuid());
        $this->assertInstanceOf(\DateTimeInterface::class, $response->getTimestamp());

        $error = $response->getError();

        $this->assertEquals(32, $error->getCode());
    }

    final public function testReport(): void
    {
        $this->setUpTestReport();

        $token = $this->requestToken();

        $request = $this->createRegisterRequest();

        $registerResponse = $this->api->sell($this->getGroupCode(), $token, $request);

        $timeout = 300;

        $start = time();
        while (time() - $start < $timeout) {
            $response = $this->api->report($this->getGroupCode(), $token, $registerResponse->getUuid());

            if ($response->getStatus() == Status::DONE()) {
                $payload = $response->getPayload();
                $this->assertNotNull($payload);

                return;
            }

            if ($response->getStatus() == Status::WAIT()) {
                $error = $response->getError();
                $this->assertNotNull($error);

                $this->assertEquals(34, $error->getCode());

                sleep(1);
            }

            if ($response->getStatus() == Status::FAIL()) {
                $this->fail('Fiscal document is failed');
            }
        }

        $this->fail('Fiscal document was not moved to done state before timeout');
    }

    private function requestToken(): string
    {
        $request = new GetTokenRequest(
            $this->getLogin(),
            $this->getPassword()
        );

        $response = $this->api->getToken($request);

        $this->assertNotNull($response->getToken());
        $this->assertNull($response->getError());

        return $response->getToken();
    }

    private function createRegisterRequest(): RegisterRequest
    {
        return new RegisterRequest(
            'test-' . md5((string) microtime(true)),
            new Receipt(
                new ClientDto(
                    'test@test.ru',
                    ''
                ),
                new Company(
                    'test@test.ru',
                    '5544332219',
                    'https://v4.online.atol.ru'
                ),
                [
                    (new Item(
                        'Test item',
                        1000.1,
                        1,
                        1000.1,
                        PaymentMethod::FULL_PAYMENT(),
                        new Vat(
                            VatType::VAT118(),
                            152.55
                        )
                    ))
                        ->setMeasurementUnit('шт.')
                        ->setPaymentObject(PaymentObject::COMMODITY())
                        ->setAgentInfo(
                            (new AgentInfo(AgentType::PAYING_AGENT()))
                                ->setPayingAgent(new PayingAgent(
                                    'test',
                                    ['+79101234567']
                                ))
                        )
                        ->setSupplierInfo(
                            new SupplierInfo(
                                ['+79101234567'],
                                'Test supplier',
                                '7705935687'
                            )
                        ),
                ],
                [
                    new Payment(
                        PaymentType::ELECTRONIC(),
                        1000.1
                    ),
                ],
                1000.1
            ),
            new \DateTime()
        );
    }

    private function createInvalidRegisterRequest(): RegisterRequest
    {
        return new RegisterRequest(
            'test-' . md5((string) microtime(true)),
            new Receipt(
                new ClientDto(
                    'test@test.ru',
                    ''
                ),
                new Company(
                    'test@test.ru',
                    '5544332219',
                    'https://v4.online.atol.ru'
                ),
                [
                    new Item(
                        'Test item',
                        1000.1,
                        1,
                        1000.1,
                        PaymentMethod::FULL_PAYMENT(),
                        new Vat(
                            VatType::VAT118(),
                            152.55
                        )
                    ),
                ],
                [
                    new Payment(
                        PaymentType::ELECTRONIC(),
                        1000.1
                    ),
                ],
                -1000.1
            ),
            new \DateTime()
        );
    }
}
