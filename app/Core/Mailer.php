<?php

declare(strict_types = 1);

namespace App\Core;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;

class Mailer implements MailerInterface
{
    /**
     * @throws FilesystemException
     */
    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        $adapter    = new LocalFilesystemAdapter(STORAGE_PATH . '/mail');
        $filesystem = new Filesystem($adapter);

        $filesystem->write(time() . '_' . uniqid('', true) . '.eml', $message->toString());
    }
}
