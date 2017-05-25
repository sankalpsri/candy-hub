<?php

namespace GetCandy\Http\Controllers\Api;

use GetCandy\Exceptions\InvalidLanguageException;
use GetCandy\Exceptions\MinimumRecordRequiredException;
use GetCandy\Http\Requests\Api\ProductFamilies\CreateRequest;
use GetCandy\Http\Requests\Api\ProductFamilies\DeleteRequest;
use GetCandy\Http\Requests\Api\ProductFamilies\UpdateRequest;
use GetCandy\Http\Transformers\Fractal\ProductFamilyTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductFamilyController extends BaseController
{
    /**
     * Handles the request to show all product families
     * @param  Request $request
     * @return Json
     */
    public function index(Request $request)
    {
        $paginator = app('api')->productFamilies()->getPaginatedData($request->per_page);
        // event(new ViewProductEvent(['hello' => 'there']));
        return $this->respondWithCollection($paginator, new ProductFamilyTransformer);
    }

    /**
     * Handles the request to show a product family based on hashed ID
     * @param  String $id
     * @return Json
     */
    public function show($id)
    {
        try {
            $product = app('api')->productFamilies()->getByHashedId($id);
        } catch (ModelNotFoundException $e) {
            return $this->errorNotFound();
        }
        return $this->respondWithItem($product, new ProductFamilyTransformer);
    }

    /**
     * Handles the request to create a new product family
     * @param  CreateRequest $request
     * @return Json
     */
    public function store(CreateRequest $request)
    {
        try {
            $result = app('api')->productFamilies()->create($request->all());
        } catch (InvalidLanguageException $e) {
            return $this->errorUnprocessable($e->getMessage());
        }
        return $this->respondWithItem($result, new ProductFamilyTransformer);
    }

    /**
     * Handles the request to update a product family
     * @param  String        $id
     * @param  UpdateRequest $request
     * @return Json
     */
    public function update($id, UpdateRequest $request)
    {
        try {
            $result = app('api')->productFamilies()->update($id, $request->all());
        } catch (MinimumRecordRequiredException $e) {
            return $this->errorUnprocessable($e->getMessage());
        } catch (NotFoundHttpException $e) {
            return $this->errorNotFound();
        } catch (InvalidLanguageException $e) {
            return $this->errorUnprocessable($e->getMessage());
        }
        return $this->respondWithItem($result, new ProductFamilyTransformer);
    }

    /**
     * Handles the request to delete a product family
     * @param  String        $id
     * @param  DeleteRequest $request
     * @return Json
     */
    public function destroy($id, DeleteRequest $request)
    {
        try {
            $result = app('api')->productFamilies()->delete($id);
        } catch (MinimumRecordRequiredException $e) {
            return $this->errorUnprocessable($e->getMessage());
        } catch (NotFoundHttpException $e) {
            return $this->errorNotFound();
        }
        return $this->respondWithNoContent();
    }
}
