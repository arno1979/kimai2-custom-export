<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\CustomExportBundle\EventSubscriber;

use App\Entity\MetaTableTypeInterface;
use App\Entity\ProjectMeta;
use App\Event\ProjectMetaDefinitionEvent;
use App\Event\ProjectMetaDisplayEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

class MetaFieldSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProjectMetaDefinitionEvent::class => ['loadProjectMeta', 200],
            ProjectMetaDisplayEvent::class => ['loadProjectFields', 200],
        ];
    }

    private function getMetaField(): MetaTableTypeInterface
    {
        $definition = new ProjectMeta();
        $definition->setName('custom_export_filename');
        $definition->setLabel('Leistungsnachweis: Dateiname');
        $definition->setOptions(['help' => 'Erlaubt es einen Dateinamen für den Custom PDF Export für diese Projekt anzugeben']);
        $definition->setType(TextType::class);
        $definition->addConstraint(new Length(['max' => 255]));
        $definition->setIsVisible(true);

        return $definition;
    }

    public function loadProjectMeta(ProjectMetaDefinitionEvent $event): void
    {
        $event->getEntity()->setMetaField($this->getMetaField());
    }

    public function loadProjectFields(ProjectMetaDisplayEvent $event): void
    {
        $event->addField($this->getMetaField());
    }

}
