<?php
declare(strict_types = 1);

namespace tests\AppBundle\Controller;

// tests directory is not available to the autoloader, so we have to manually require these files:
require_once 'DataFixtures/LoadActivityData.php';

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ActivityControllerTest extends WebTestCase
{
    public function setUp()
    {
        // empty database before each test.
        // any test that needs data to function has to specify the data needed explicitly.
        $this->loadFixtures([]);
    }

    public function testActivityNameEnglish()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities/32');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            'Emoticon Project Gauge',
            $activity['name']
        );

        $client->request('GET', '/activities/59');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            'Happiness Histogram',
            $activity['name']
        );

        $client->request('GET', '/activities/80');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            'Repeat &amp; Avoid',
            $activity['name']
        );
    }

    public function testActivityNameGerman()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities/32?locale=de');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            'Projekt-Gef&uuml;hlsmesser',
            $activity['name']
        );

        $client->request('GET', '/activities/58?locale=de');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            'Verdeckter Boss',
            $activity['name']
        );

        $client->request('GET', '/activities/75?locale=de');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            'Schreibe das Unaussprechliche',
            $activity['name']
        );
    }

    public function testActivitySourceSimpleString()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities/17');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            '<a href="http://fairlygoodpractices.com/samolo.htm">Fairly good practices</a>',
            $activity['source']
        );

        $client->request('GET', '/activities/80');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            '<a href="http://www.infoq.com/minibooks/agile-retrospectives-value">Luis Goncalves</a>',
            $activity['source']
        );
    }

    public function testActivitySourcePlaceholder()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities/77');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            '<a href="https://leanpub.com/ErfolgreicheRetrospektiven">Judith Andresen</a>',
            $activity['source']
        );

        $client->request('GET', '/activities/5');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            '<a href="http://www.finding-marbles.com/">Corinna Baldauf</a>',
            $activity['source']
        );
    }

    public function testActivitySourcePlaceholderAndString()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities/15');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            '<a href="http://www.amazon.com/Agile-Retrospectives-Making-Teams-Great/dp/0977616649/">Agile Retrospectives</a> who took it from \'The Satir Model: Family Therapy and Beyond\'',
            $activity['source']
        );

        $client->request('GET', '/activities/37');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            '<a href="http://www.amazon.com/Innovation-Games-Creating-Breakthrough-Collaborative/dp/0321437292/">Luke Hohmann</a>, found at <a href="http://www.ayeconference.com/appreciativeretrospective/">Diana Larsen</a>',
            $activity['source']
        );
    }

    public function testActivitySourceStringAndPlaceholder()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities/14');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            'ALE 2011, <a href="http://www.finding-marbles.com/">Corinna Baldauf</a>',
            $activity['source']
        );

        $client->request('GET', '/activities/65');
        $activity = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            '<a href="http://blog.8thlight.com/doug-bradbury/2011/09/19/apreciative_inquiry_retrospectives.html">Doug Bradbury</a>, adapted for SW development by <a href="http://www.finding-marbles.com/">Corinna Baldauf</a>',
            $activity['source']
        );
    }

    public function testExpandedActivitySourceInCollectionRequests()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities');
        $activities = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            'ALE 2011, <a href="http://www.finding-marbles.com/">Corinna Baldauf</a>',
            $activities[14 - 1]['source']
        );

        $this->assertEquals(
            '<a href="http://blog.8thlight.com/doug-bradbury/2011/09/19/apreciative_inquiry_retrospectives.html">Doug Bradbury</a>, adapted for SW development by <a href="http://www.finding-marbles.com/">Corinna Baldauf</a>',
            $activities[65 - 1]['source']
        );
    }

    public function testActivityIdsAndNamesInCollectionRequestsEnglish()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities');
        $activities = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(1, $activities[1 - 1]['retromatId']);
        $this->assertEquals(32, $activities[32 - 1]['retromatId']);
        $this->assertEquals(100, $activities[100 - 1]['retromatId']);

        $this->assertEquals('Emoticon Project Gauge', $activities[32 - 1]['name']);
        $this->assertEquals('Happiness Histogram', $activities[59 - 1]['name']);
        $this->assertEquals('Repeat &amp; Avoid', $activities[80 - 1]['name']);
    }

    public function testActivityIdsAndNamesInCollectionRequestsGerman()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities?locale=de');
        $activities = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(1, $activities[1 - 1]['retromatId']);
        $this->assertEquals(32, $activities[32 - 1]['retromatId']);
        $this->assertEquals(75, $activities[75 - 1]['retromatId']);

        $this->assertEquals('Projekt-Gef&uuml;hlsmesser', $activities[32 - 1]['name']);
        $this->assertEquals('Verdeckter Boss', $activities[58 - 1]['name']);
        $this->assertEquals('Schreibe das Unaussprechliche', $activities[75 - 1]['name']);
    }

    public function testOnlyTranslatedActivitiesInCollectionRequests()
    {
        $this->loadFixtures(['tests\AppBundle\Controller\DataFixtures\LoadActivityData']);
        $client = static::createClient();

        $client->request('GET', '/activities?locale=de');
        $activities = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(75, $activities);

        $client->request('GET', '/activities?locale=en');
        $activities = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(131, $activities);

        $client->request('GET', '/activities?locale=es');
        $activities = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(95, $activities);

        $client->request('GET', '/activities?locale=fr');
        $activities = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(50, $activities);

        $client->request('GET', '/activities?locale=nl');
        $activities = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(101, $activities);
    }
}