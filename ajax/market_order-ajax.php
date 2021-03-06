<?php
/**
 * Created by PhpStorm.
 * User: Abhishek Sinha
 * Date: 11/26/2016
 * Time: 7:10 PM
 */

require_once '../includes/autoload.php';

if (!isset($_SESSION['user_id'])) {
    return false;
} else {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
}

if(isset($_POST['job']) && $_POST['job'] == 'market_order') {

    $std = new stdClass();
    $std->user = null;
    $std->order = null;
    $std->error = false;
    $std->msg = null;

    if(class_exists('Orders') && class_exists('Users')) {

        $customer = new Users();
        $validate_user = $customer->check_user($user_id);

        if($validate_user == "" || empty($validate_user)) {
            $std->error = true;
            $std->msg = "No such user exist. Please login again.";
            echo json_encode($std);
            return false;
        }

    } else {
        return false;
    }

    $std->user = $validate_user;

    if(isset($_POST['qty'], $_POST['type'])) {
        $qty = (float)$_POST['qty'];
        $order_type = $_POST['type'];

        if($qty > 0) {
            if(is_string($order_type)) {
                if(trim($order_type) == 'market_buy_btn' || trim($order_type) == 'market_sell_btn') {

                    if ($order_type == 'market_buy_btn') {
                        $order_type = 'buy';
                    } elseif($order_type == 'market_sell_btn') {
                        $order_type = 'sell';
                    } else {
                        $std->error = true;
                        $std->msg = 'Invalid Order type';
                        echo json_encode($std);
                        return false;
                    }

                    $Orders = new Orders();
                    $run_market_order = $Orders->market_order($order_type, $qty);

                    $std->user = $validate_user;
                    $std->order = $run_market_order;
                    $std->error = false;
                    $std->msg = 'Success';
                }
            }
        } else {
            $std->error = true;
            $std->msg = 'Please insert a valid quantity.';
        }
    }
    echo json_encode($std);
} else {
    return false;
}