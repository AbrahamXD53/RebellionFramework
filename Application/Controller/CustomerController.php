<?php 

namespace Application\Controller;
use Application\Entity\{Customer,Profile};
use View;
use Server;

class CustomerController{

    
    public function getCustomer($id)
    {
        $response = Server::getResponse();
        $customer=Customer::find($id);

        $response->setData($customer);
        if($customer)
        $response->setStatus(302);
        else
        $response->setStatus(404);
        return $response->getData();
        //return View::render('index.html', array('name' => Customer::find($id) ));;
    }
    public function getProfile($id)
    {
        $response = Server::getResponse();
        $profile=Profile::find($id);
        $profile->customer()->purchases();
        $response->setData($profile);
        if($profile)
        $response->setStatus(302);
        else
        $response->setStatus(404);
        return $response->getData();
        //return View::render('index.html', array('name' => Customer::find($id) ));;
    }
    public function postCustomer()
    {
        $request=Server::getRequest();
        var_dump($request);
        return \json_encode($request->getData());
    }
}