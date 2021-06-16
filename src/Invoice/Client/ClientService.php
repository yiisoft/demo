<?php

declare(strict_types=1);

namespace App\Invoice\Client;

use App\Invoice\Entity\Client;

final class ClientService
{
    private ClientRepository $repository;

    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveClient(Client $model, ClientForm $form): void
    {
        $model->setClient_name($form->getClient_name());
        $model->setClient_address_1($form->getClient_address_1());
        $model->setClient_address_2($form->getClient_address_2());
        $model->setClient_city($form->getClient_city());
        $model->setClient_state($form->getClient_state());
        $model->setClient_zip($form->getClient_zip());
        $model->setClient_country($form->getClient_country());
        $model->setClient_phone($form->getClient_phone());
        $model->setClient_fax($form->getClient_fax());
        $model->setClient_mobile($form->getClient_mobile());
        $model->setClient_email($form->getClient_email());
        $model->setClient_web($form->getClient_web());
        $model->setClient_vat_id($form->getClient_vat_id());
        $model->setClient_tax_code($form->getClient_tax_code());
        $model->setClient_language($form->getClient_language());
        $model->setClient_active($form->getClient_active());
        $model->setClient_surname($form->getClient_surname());
        $model->setClient_avs($form->getClient_avs());
        $model->setClient_insurednumber($form->getClient_insurednumber());
        $model->setClient_veka($form->getClient_veka());
        $model->setClient_birthdate($form->getClient_birthdate());
        $model->setClient_gender($form->getClient_gender());
        
        if ($model->isNewRecord()) {
            $model->setClient_active(true);
        }

        $this->repository->save($model);
    }
    
    public function deleteClient(Client $model): void
    {
        $this->repository->delete($model);
    }
}
