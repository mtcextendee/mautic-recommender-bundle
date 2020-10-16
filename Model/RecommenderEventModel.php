<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Model;

use Mautic\CoreBundle\Model\AjaxLookupModelInterface;
use Mautic\CoreBundle\Model\FormModel;
use MauticPlugin\MauticRecommenderBundle\Entity\Event;
use MauticPlugin\MauticRecommenderBundle\Entity\EventRepository;
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplateRepository;
use MauticPlugin\MauticRecommenderBundle\Event\RecommenderEvent;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderEventType;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class RecommenderEventModel extends FormModel implements AjaxLookupModelInterface
{
    /**
     * Retrieve the permissions base.
     *
     * @return string
     */
    public function getPermissionBase()
    {
        return 'recommender:recommender';
    }

    /**
     * {@inheritdoc}
     *
     * @return EventRepository
     */
    public function getRepository()
    {
        /** @var RecommenderTemplateRepository $repo */
        $repo = $this->em->getRepository('MauticRecommenderBundle:Event');

        $repo->setTranslator($this->translator);

        return $repo;
    }

    /**
     * Here just so PHPStorm calms down about type hinting.
     *
     * @param null $id
     *
     * @return Event|null
     */
    public function getEntity($id = null)
    {
        if (null === $id) {
            return new Event();
        }

        return parent::getEntity($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param       $entity
     * @param       $formFactory
     * @param null  $action
     * @param array $options
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function createForm($entity, $formFactory, $action = null, $options = [])
    {
        if (!$entity instanceof Event) {
            throw new \InvalidArgumentException('Entity must be of class Event');
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(RecommenderEventType::class, $entity, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @param $action
     * @param $entity
     * @param $isNew
     * @param $event
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function dispatchEvent($action, &$entity, $isNew = false, \Symfony\Component\EventDispatcher\Event $event = null)
    {
        if (!$entity instanceof Event) {
            throw new MethodNotAllowedHttpException(['Event']);
        }

        switch ($action) {
            case 'pre_save':
                $name = RecommenderEvents::PRE_SAVE;
                break;
            case 'post_save':
                $name = RecommenderEvents::POST_SAVE;
                break;
            case 'pre_delete':
                $name = RecommenderEvents::PRE_DELETE;
                break;
            case 'post_delete':
                $name = RecommenderEvents::POST_DELETE;
                break;
            default:
                return null;
        }

        if ($this->dispatcher->hasListeners($name)) {
            if (empty($event)) {
                $event = new RecommenderEvent($entity, $isNew);
                $event->setEntityManager($this->em);
            }

            $this->dispatcher->dispatch($name, $event);

            return $event;
        } else {
            return null;
        }
    }

    /**
     * @param        $type
     * @param string $filter
     * @param int    $limit
     * @param int    $start
     * @param array  $options
     */
    public function getLookupResults($type, $filter = '', $limit = 10, $start = 0, $options = [])
    {
        $results = [];
        switch ($type) {
            case 'recommender_event':
                $entities = $this->getRepository()->getRecommenderList(
                    $filter,
                    $limit,
                    $start,
                    $this->security->isGranted($this->getPermissionBase().':viewother'),
                    isset($options['top_level']) ? $options['top_level'] : false,
                    isset($options['ignore_ids']) ? $options['ignore_ids'] : []
                );

                foreach ($entities as $entity) {
                    $results[$entity['language']][$entity['id']] = $entity['name'];
                }

                //sort by language
                ksort($results);

                break;
        }

        return $results;
    }
}
