<?php


namespace MailerTiny\Http\Controllers\API;


use MailerTiny\Components\Subscriber\FieldsManager;
use MailerTiny\Core\Request;
use MailerTiny\Http\Response\EmptyJsonSerializible;
use MailerTiny\Http\Response\JsonResponse;

class FieldsController
{
    /**
     * @var FieldsManager
     */
    private $fieldsManager;

    /**
     * SubscribersController constructor.
     */
    public function __construct()
    {
        $this->fieldsManager = new FieldsManager();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $collection = $this->fieldsManager->getAll();

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
        $subscriber = $this->fieldsManager->createField($data);

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
        $subscriber = $this->fieldsManager->findFieldById($id);

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

        $subscriber = $this->fieldsManager->updateField($id,
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
        $this->fieldsManager->deleteField($id);

        return new JsonResponse(new EmptyJsonSerializible([
            'success' => true
        ]));
    }
}