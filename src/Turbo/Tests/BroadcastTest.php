<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\UX\Turbo\Tests;

use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\PantherTestCase;

/**
 * @author Kévin Dunglas <kevin@dunglas.fr>
 */
class BroadcastTest extends PantherTestCase
{
    private const BOOK_TITLE = 'The Ecology of Freedom: The Emergence and Dissolution of Hierarchy';
    private const AUTHOR_NAME_1 = 'Murray Bookchin';
    private const AUTHOR_NAME_2 = 'John Doe';

    protected function setUp(): void
    {
        if (!file_exists(__DIR__.'/app/public/build')) {
            throw new \Exception(sprintf('Move into %s and execute Encore before running this test.', realpath(__DIR__.'/app')));
        }

        parent::setUp();
    }

    public function testBroadcast(): void
    {
        ($client = self::createPantherClient())->request('GET', '/books');

        $crawler = $client->submitForm('Submit', ['title' => self::BOOK_TITLE]);

        $this->assertSelectorWillContain('#books', self::BOOK_TITLE);
        if (!preg_match('/\(#(\d+)\)/', $crawler->filter('#books div')->text(), $matches)) {
            $this->fail('ID not found');
        }

        $client->submitForm('Submit', ['id' => $matches[1], 'title' => 'updated']);
        $this->assertSelectorWillContain('#books', 'updated');

        $client->submitForm('Submit', ['id' => $matches[1], 'remove' => 'remove']);
        $this->assertSelectorWillNotContain('#books', $matches[1]);
    }

    public function testScopedBroadcast(): void
    {
        ($client = self::createPantherClient())->request('GET', '/authors');

        $client->submitForm('Submit', ['name' => self::AUTHOR_NAME_1]);
        $client->waitForElementToContain('#authors div:nth-child(1)', self::AUTHOR_NAME_1);
        $client->submitForm('Submit', ['name' => self::AUTHOR_NAME_2]);
        $client->waitForElementToContain('#authors div:nth-child(2)', self::AUTHOR_NAME_2);

        $crawlerAuthor = $client->getCrawler();

        $this->assertSelectorWillContain('#authors', self::AUTHOR_NAME_1);
        if (!preg_match_all('/\(#(\d+)\)/', $crawlerAuthor->filter('#authors')->text(), $matches)) {
            $this->fail('IDs of authors not found');
        }

        $author1Id = $matches[1][0];
        $author2Id = $matches[1][1];

        ($clientAuthor1 = self::createAdditionalPantherClient())->request('GET', '/authors/' . $author1Id);
        ($clientAuthor2 = self::createAdditionalPantherClient())->request('GET', '/authors/' . $author2Id);

        $client->request('GET', '/books');

        $client->submitForm('Submit', ['title' => self::BOOK_TITLE, 'authorId' => $author1Id]);

        $clientAuthor1->waitForElementToContain('#books div', self::BOOK_TITLE);

        $booksElement = $clientAuthor2->findElement(WebDriverBy::cssSelector('#books'));

        $this->assertStringNotContainsString(
            self::BOOK_TITLE,
            $booksElement->getText(),
            'Author 2 shows book that does not belong to them'
        );
    }
}
