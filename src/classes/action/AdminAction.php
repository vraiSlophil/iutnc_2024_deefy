<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authn;
use iutnc\deefy\database\DeefyRepository;

class AdminAction extends Action
{
    private DeefyRepository $deefyRepository;

    public function __construct()
    {
        parent::__construct();
        $this->deefyRepository = DeefyRepository::getInstance();

    }

    public function execute(): string
    {
        $user = Authn::getAuthenticatedUser();

        if ($user->hasAccess(100)) {
            return $this->renderAdminPanel();
        } else {
            return '<div>Accès refuser.</div>';
        }
    }

    private function renderAdminPanel(): string
    {
        $output = '<h1>Admin Panel</h1>';

        $userList = $this->deefyRepository->getUserList();

        $output .= '<pre>' . print_r($userList, true) . '</pre>';

//        foreach ($userList as $userDataInArray) {
//            $output .= '<div>
//                <h2>' . $userDataInArray['user_name'] . '</h2>
//                <p>' . $userDataInArray['user_id'] . '</p>
//                <p>' . $userDataInArray['user_email'] . '</p>
//                <p>' . $userDataInArray['permission_id'] . '</p>
//                <p>' . $userDataInArray['role_name'] . '</p>
//                <p>' . $userDataInArray['role_level'] . '</p>
//
//            </div>';
//        }






        return $output;
    }
}