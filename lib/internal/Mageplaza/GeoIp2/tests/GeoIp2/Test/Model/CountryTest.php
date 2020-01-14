<?php

namespace GeoIp2\Test\Model;

use GeoIp2\Model\Country;

/**
 * @coversNothing
 */
class CountryTest extends \PHPUnit_Framework_TestCase
{
    private $raw = [
        'continent' => [
            'code' => 'NA',
            'geoname_id' => 42,
            'names' => ['en' => 'North America'],
        ],
        'country' => [
            'geoname_id' => 1,
            'iso_code' => 'US',
            'names' => ['en' => 'United States of America'],
        ],
        'registered_country' => [
            'geoname_id' => 2,
            'is_in_european_union' => true,
            'iso_code' => 'DE',
            'names' => ['en' => 'Germany'],
        ],
        'traits' => [
            'ip_address' => '1.2.3.4',
        ],
    ];

    private $model;

    public function setUp()
    {
        $this->model = new Country($this->raw, ['en']);
    }

    public function testObjects()
    {
        $this->assertInstanceOf(
            'GeoIp2\Model\Country',
            $this->model,
            'minimal GeoIp2::Model::Country object'
        );
        $this->assertInstanceOf(
            'GeoIp2\Record\Continent',
            $this->model->continent
        );
        $this->assertInstanceOf(
            'GeoIp2\Record\Country',
            $this->model->country
        );
        $this->assertInstanceOf(
            'GeoIp2\Record\Country',
            $this->model->registeredCountry
        );
        $this->assertInstanceOf(
            'GeoIp2\Record\RepresentedCountry',
            $this->model->representedCountry
        );
        $this->assertInstanceOf(
            'GeoIp2\Record\Traits',
            $this->model->traits
        );
    }

    public function testValues()
    {
        $this->assertSame(
            42,
            $this->model->continent->geonameId,
            'continent geoname_id is 42'
        );

        $this->assertSame(
            'NA',
            $this->model->continent->code,
            'continent code is NA'
        );

        $this->assertSame(
            ['en' => 'North America'],
            $this->model->continent->names,
            'continent names'
        );

        $this->assertSame(
            'North America',
            $this->model->continent->name,
            'continent name is North America'
        );

        $this->assertSame(
            1,
            $this->model->country->geonameId,
            'country geoname_id is 1'
        );

        $this->assertFalse(
            $this->model->country->isInEuropeanUnion,
            'country is_in_european_union is false'
        );

        $this->assertSame(
            'US',
            $this->model->country->isoCode,
            'country iso_code is US'
        );

        $this->assertSame(
            ['en' => 'United States of America'],
            $this->model->country->names,
            'country name'
        );

        $this->assertSame(
            $this->model->country->name,
            'United States of America',
            'country name is United States of America'
        );

        $this->assertNull(
            $this->model->country->confidence,
            'country confidence is undef'
        );

        $this->assertSame(
            2,
            $this->model->registeredCountry->geonameId,
            'registered_country geoname_id is 2'
        );

        $this->assertTrue(
            $this->model->registeredCountry->isInEuropeanUnion,
            'registered_country is_in_european_union is true'
        );

        $this->assertSame(
            'DE',
            $this->model->registeredCountry->isoCode,
            'registered_country iso_code is Germany'
        );

        $this->assertSame(
            ['en' => 'Germany'],
            $this->model->registeredCountry->names,
            'registered_country names'
        );

        $this->assertSame(
            'Germany',
            $this->model->registeredCountry->name,
            'registered_country name is Germany'
        );

        foreach (['isAnonymousProxy', 'isSatelliteProvider'] as $meth) {
            $this->assertFalse(
                $this->model->traits->$meth,
                "traits $meth returns 0 by default"
            );
        }

        $this->assertSame(
            $this->raw,
            $this->model->raw,
            'raw method returns raw input'
        );
    }

    public function testJsonSerialize()
    {
        $this->assertSame(
            $this->raw,
            $this->model->jsonSerialize(),
            'jsonSerialize returns initial array'
        );

        $this->assertSame(
            $this->raw['country'],
            $this->model->country->jsonSerialize(),
            'jsonSerialize returns initial array for the record'
        );

        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4+.');
        }

        $this->assertSame(
            json_encode($this->raw),
            json_encode($this->model),
            'json_encode can be called on the model object directly'
        );

        $this->assertSame(
            json_encode($this->raw['country']),
            json_encode($this->model->country),
            'json_encode can be called on the record object directly'
        );
    }

    public function testIsSet()
    {
        $this->assertTrue(isset($this->model->traits), 'traits is set');
        $this->assertFalse(isset($this->model->unknown), 'unknown is not set');

        $this->assertTrue(
            isset($this->model->traits->ipAddress),
            'ip_address is set'
        );
        $this->assertFalse(
            isset($this->model->traits->unknown),
            'unknown trait is not set'
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown attribute
     */
    public function testUnknownRecord()
    {
        $this->model->unknownRecord;
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown attribute
     */
    public function testUnknownTrait()
    {
        $this->model->traits->unknown;
    }
}
