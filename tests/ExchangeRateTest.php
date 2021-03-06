<?php
namespace Kuartet\BI;

use \Carbon\Carbon;
use \Kuartet\BI\Domain\Rate;
use \PHPUnit_Framework_TestCase;

class ExchangeRateTest extends PHPUnit_Framework_TestCase
{
    public function testGetFetcher()
    {
        $exchangeRate = new ExchangeRate();
        $this->assertInstanceOf('\Kuartet\BI\Fetcher\GuzzleFetcher', $exchangeRate->getFetcher());
    }

    public function testGetParser()
    {
        $exchangeRate = new ExchangeRate();
        $this->assertInstanceOf('\Kuartet\BI\Parser\DomCrawlerParser', $exchangeRate->getParser());
    }

    public function testGetUpdates()
    {
        $responseBody = 'somehtmlcontent';

        $fetcher = $this->getMockBuilder('\Kuartet\BI\Fetcher\FetcherInterface')
            ->getMock();

        $fetcher->expects($this->once())
            ->method('fetch')
            ->willReturn($responseBody);

        $updatedAt = Carbon::parse('11 November 2014');
        $expectedRates = [
            new Rate('AUD', 'AUSTRALIAN DOLLAR', 10571.32, 10460.97, $updatedAt),
            new Rate('JPY', 'JAPANESE YEN', 10655.51/100, 10546.41/100, $updatedAt),
            new Rate('USD', 'US DOLLAR', 12224.00, 12102.00, $updatedAt)
        ];

        $parser = $this->getMockBuilder('\Kuartet\BI\Parser\ParserInterface')
            ->getMock();

        $parser->expects($this->once())
            ->method('parse')
            ->willReturn($expectedRates);

        $exchangeRate = new ExchangeRate();
        $exchangeRate->setFetcher($fetcher)
            ->setParser($parser);
        $rates = $exchangeRate->getUpdates();

        $this->assertEquals($expectedRates, $rates);
    }
}
