<?php

namespace App\Http\Controllers\v1\User\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use App\Models\Order;
use App\Helpers\CustomValidator;
use App\Helpers\OrderHelper;
use App\Traits\ResponseHandler;
use App\Http\Requests\AddItemToOrder;

class OrderController extends Controller
{
    use ResponseHandler;

    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    public function showClosedOrder(Request $request)
    {
        // $inputs = $request->all();

        // $validator_rules = [
        //     'order_id' => 'required|integer|exists:order,id'
        // ];
        // $validate_result = CustomValidator::validator($inputs, $validator_rules);

        // if($validate_result['code']!== 200){
        //     return ResponseHandler::buildUnsuccessfulValidationResponse($validate_result);
        // }

        $order = $this->model->getPastOrderById($request);

        return $this->buildSuccess('success', $order, 'Order loaded successfully', Response::HTTP_OK);
    }
    public function getOpenOrder(Request $request)
    {
        $orders = $this->model->searchOpenOrders($request);

        return $this->buildSuccess('success', $orders, 'Orders loaded successfully', Response::HTTP_OK);
    }

    public function getClosedOrder(Request $request)
    {
        $orders = $this->model->searchClosedeOrders($request);

        return $this->buildSuccess('success', $orders, 'Orders loaded successfully', Response::HTTP_OK);
    }

    public function addItemToOrder(Request $request)
    {
        $inputs = $request->all();

        $validator_rules = [
            'product_id' => 'required|integer|exists:products,id'
        ];
        $validate_result = CustomValidator::validator($inputs, $validator_rules);

        if($validate_result['code']!== 200){
            return ResponseHandler::buildUnsuccessfulValidationResponse($validate_result);
        }

        $orders = $this->model->addItemToOrders($request);

        return $this->buildSuccess('success', [], 'Item Added successfully', Response::HTTP_OK);
    }

    public function placeOrder(Request $request)
    {
        $paymentMethods = implode(',', config('constants.payment_methods'));
        $inputs = $request->all();

        $validator_rules = [
            'order_id' => 'required|integer|exists:orders,id',
            'payment_method' => 'sometimes|required|in:'.$paymentMethods
        ];
        $validate_result = CustomValidator::validator($inputs, $validator_rules);

        if($validate_result['code']!== 200){
            return ResponseHandler::buildUnsuccessfulValidationResponse($validate_result);
        }

        //Add the product metadata to the order items table so that if the product data / price is changed, we can still see the price the user purchased the product for
        $preparedOrder = OrderHelper::processOrder($inputs['order_id']);

        $processPayment = OrderHelper::processPayment($inputs, $preparedOrder);

        if($processPayment->status == config('constants.status.payment_completed')){
            $finalorder = OrderHelper::markOrderAsPaid($preparedOrder);
        } else {
            throw new \App\Exceptions\PaymentException("Enable to process Payment. ".$processPayment->payment_response.". Please try some other payment methods");
        }

        return $this->buildSuccess('success', $finalorder, 'Item Added successfully', Response::HTTP_OK);
    }
}
