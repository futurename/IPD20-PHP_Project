<?php

namespace App\Controller;

use App\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use DB;

class AdminStoreController
{
    public function index (Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);

        $storeList = DB::query(
            "SELECT id, storeName, province, city, postCode, address, phone
             FROM stores"
             );

        return $view->render($response, 'admin/item_list.html.twig',[
            'itemTitle' => 'Store',
            'itemList' => $storeList,
            'itemKeyList' => ['id', 'storeName', 'province', 'city', 'postCode', 'address', 'phone']
        ]);
    }

    public function showAll (Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);

        $storeList = DB::query(
            "SELECT id, storeName, province, city, postCode, address, phone
             FROM stores"
             );

        return $view->render($response, 'admin/cards/item_card.html.twig', [
            'itemList' => $storeList
        ]);
    }

    public function create (Request $request, Response $response, array $args)
    {
        $jsonData = json_decode($request->getBody(), true);

        $errorList = Validator::store($jsonData);

        if(empty($errorList)){
            DB::insert('stores',$jsonData);
        }

        $response->getBody()->write(json_encode([
            'errorList' => $errorList
        ]));

        return $response;
    }

    public function delete (Request $request, Response $response, array $args)
    {
        $storeId = $args['id'];

        DB::delete('stores','id=%i',$storeId);
        $response->getBody()->write(json_encode(DB::affectedRows()));

        return $response;
    }

    public function edit (Request $request, Response $response, array $args)
    {
        $fieldList = ['id','storeName','province','city','postCode','address','phone'];
        $storeId = $args['id'];

        $jsonData = json_decode($request->getBody(), true);

        $data = [
            $fieldList[$jsonData['fieldIndex']] => $jsonData['value']
        ];

        $errorList = Validator::store($data,false);

        if(empty($errorList)){
            DB::update('stores',$data,'id=%s',$storeId);
        }

        $response->getBody()->write(json_encode([
            'errorList' => $errorList
        ]));

        return $response;
    }
}