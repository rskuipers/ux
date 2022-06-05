<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

/**
 * @ORM\Entity
 * @Broadcast(topics={"@='book_by_author_' ~ (object.author ? object.author.id : null)", "books"})
 *
 * @author Kévin Dunglas <kevin@dunglas.fr>
 */
#[Broadcast(topics: ['@="book_by_author_" ~ (object.author ? object.author.id : null)', 'books'])]
class Book
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null
     */
    public $id;

    /**
     * @ORM\Column
     *
     * @var string
     */
    public $title = '';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="books")
     *
     * @var Author|null
     */
    public $author;
}
