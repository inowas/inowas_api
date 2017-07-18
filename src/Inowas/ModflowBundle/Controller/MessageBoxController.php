<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Prooph\Common\Messaging\Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/** @noinspection LongInheritanceChainInspection */
class MessageBoxController extends InowasRestController
{
    /**
     * messageBox
     *
     * @ApiDoc(
     *      input = {
     *          "class" = "AppBundle\Dto\ProophMessageApi",
     *      },
     *      resource = true,
     *      section = "MessageBox",
     *      description = "Send a message to the app",
     *      statusCodes = {
     *          202 = "Returned when successful",
     *          500 = "Error / JSON-Schema validation failed"
     *      }
     * )
     *
     *
     * @Rest\Post("/messagebox")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \Inowas\ModflowBundle\Exception\InvalidArgumentException
     */
    public function messageBoxAction(Request $request): JsonResponse
    {

        $messageFactory = $this->get('prooph_message_factory');
        $content = $this->getContentAsArray($request);

        $this->assertContainsKey('uuid', $content);
        $this->assertContainsKey('message_name', $content);
        $this->assertContainsKey('payload', $content);

        if (isset($content['created_at'])) {
            $content['created_at'] = $this->getDatetime($content['created_at']);
        }
        $message = $messageFactory->createMessageFromArray($content['message_name'], $content);

        switch ($message->messageType()) {
            case Message::TYPE_COMMAND:
                $this->get('prooph_service_bus.modflow_command_bus')->dispatch($message);
                return new JsonResponse('', 202);

            case Message::TYPE_EVENT:
                $this->get('prooph_service_bus.modflow_event_bus')->dispatch($message);
                return new JsonResponse('', 202);

            case Message::TYPE_QUERY:
                throw InvalidArgumentException::withMessage('Invalid message type. QueryBus not supported.');

            default:
                throw InvalidArgumentException::withMessage('Invalid message type.');
        }
    }

    private function getDatetime($date): \DateTimeImmutable
    {
        if (is_string($date)) {
            return \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s.u',
                $date,
                new \DateTimeZone('UTC')
            );
        }

        if (is_array($date)) {
            return \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s.u',
                $date['date'],
                new \DateTimeZone('UTC')
            );
        }

        throw InvalidArgumentException::withMessage('Invalid date format in message.');
    }
}
