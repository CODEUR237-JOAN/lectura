<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Book;

class BookTest extends TestCase
{
    /**
     * Teste si la methode isPdf fonctionne correctement.
     *
     * @return void
     */
    public function test_is_pdf_returns_true_for_pdf_format()
    {
        $book = new Book();
        $book->file_format = 'pdf';

        $this->assertTrue($book->isPdf());
        $this->assertFalse($book->isEpub());
    }

    /**
     * Teste si la methode isEpub fonctionne correctement.
     *
     * @return void
     */
    public function test_is_epub_returns_true_for_epub_format()
    {
        $book = new Book();
        $book->file_format = 'epub';

        $this->assertTrue($book->isEpub());
        $this->assertFalse($book->isPdf());
    }

    /**
     * Teste si la methode hasRemoteFile detecte un lien distant.
     *
     * @return void
     */
    public function test_has_remote_file_detects_url()
    {
        $book = new Book();
        
        $book->fichier_path = 'https://example.com/livre.pdf';
        $this->assertTrue($book->hasRemoteFile());

        $book->fichier_path = 'storage/livres/mon_livre.epub';
        $this->assertFalse($book->hasRemoteFile());
    }
}
