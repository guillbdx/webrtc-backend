<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Form\FormEventSubscriber;

use App\Entity\Photo;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class NormalizePhotoSubscriber implements EventSubscriberInterface
{

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => ['normalizePhoto', -1000000]
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function normalizePhoto(FormEvent $event): void
    {
        /** @var Photo $photo */
        $photo = $event->getData();

        /** We create the gd image */
        $base64 = $photo->getBase64();
        $base64 = str_replace('data:image/jpeg;base64,', '', $base64);
        $blob = base64_decode($base64);
        $gdImage = imagecreatefromstring($blob);

        /** We change the image quality */
        ob_start ();
        imagejpeg ($gdImage, null, 72);
        $blob = ob_get_contents ();
        ob_end_clean ();

        /** We set the new value */
        $base64 = base64_encode($blob);
        $photo->setBase64($base64);
        $event->setData($photo);
    }

}