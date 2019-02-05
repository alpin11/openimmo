<?php
namespace Ujamii\OpenImmo\Tests\Generator;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\SerializerInterface;
use Ujamii\OpenImmo\API\Anhang;
use Ujamii\OpenImmo\API\Openimmo;
use Ujamii\OpenImmo\API\Uebertragung;

/**
 * Class SerializerTest
 * @package Ujamii\OpenImmo\Tests\Generator
 */
class DeSerializerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     *
     */
    public function setUp()
    {
        $this->serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        // @see https://stackoverflow.com/questions/14629137/jmsserializer-stand-alone-annotation-does-not-exist-or-cannot-be-auto-loaded
        AnnotationRegistry::registerLoader('class_exists');
    }

    public function testReadXml()
    {
        $xmlString = file_get_contents('./example/openimmo-data_127.xml');

        $uebertragung = new Uebertragung();
        $uebertragung
            ->setArt(Uebertragung::ART_ONLINE)
            ->setUmfang(Uebertragung::UMFANG_VOLL)
            ->setModus(Uebertragung::MODUS_NEW)
            ->setVersion('1.2.7')
            ->setSendersoftware('OIGEN')
            ->setSenderversion('0.9')
            ->setTechnEmail('')
            ->setTimestamp(new \DateTime('2014-06-01T10:00:00'))
            ->setRegiId('ABCD143');

        /* @var $openImmo Openimmo */
        $openImmo = $this->serializer->deserialize($xmlString, OpenImmo::class, 'xml');
        $this->assertEquals($uebertragung, $openImmo->getUebertragung());
//var_dump($openImmo);
        $this->assertEquals('ABCD13', $openImmo->getAnbieter()->getLizenzkennung());

    }

    public function testReadAnhangXml()
    {
        $xmlString = '<anhang location="EXTERN" gruppe="BILD">
      <anhangtitel />
      <format />
      <daten>
        <pfad>/dev/null</pfad>
      </daten>
    </anhang>';

        /* @var $anhang Anhang */
        $anhang = $this->serializer->deserialize($xmlString, Anhang::class, 'xml');

        $this->assertEquals(Anhang::LOCATION_EXTERN, $anhang->getLocation());
        $this->assertEquals(Anhang::GRUPPE_BILD, $anhang->getGruppe());
        $this->assertEquals('/dev/null', $anhang->getDaten()->getPfad());
    }

}