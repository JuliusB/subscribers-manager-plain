<?php


namespace MailerTiny\Http\Controllers\API;


use MailerTiny\Components\Subscriber\SubscribersManager;
use MailerTiny\Core\Request;
use MailerTiny\Http\Response\EmptyJsonSerializible;
use MailerTiny\Http\Response\JsonResponse;

class SubscribersController
{
    /**
     * @var SubscribersManager
     */
    private $subscribersManager;

    /**
     * SubscribersController constructor.
     */
    public function __construct()
    {
        $this->subscribersManager = new SubscribersManager();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $collection = $this->subscribersManager->getAll();

        return new JsonResponse($collection);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->inputs();

        $subscriber = $this->subscribersManager->createSubscriber($data);

        return new JsonResponse($subscriber);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        $subscriber = $this->subscribersManager->findSubscriberById($id);

        return new JsonResponse($subscriber);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->inputs();

        $subscriber = $this->subscribersManager->updateSubscriber($id,
            $data);

        return new JsonResponse($subscriber);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $this->subscribersManager->deleteSubscriber($id);

        return new JsonResponse(new EmptyJsonSerializible([
            'success' => true
        ]));
    }
}