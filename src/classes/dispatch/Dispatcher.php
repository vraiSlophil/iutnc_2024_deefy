<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\AdminAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\LoginAction;
use iutnc\deefy\action\LogoutAction;
use iutnc\deefy\action\RegisterAction;
use iutnc\deefy\auth\Authz;

class Dispatcher {
    private ?string $action;

    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void {

        if (!in_array($this->action, ['login', 'register', 'default', 'logout'])) {
            if (!Authz::validateToken()) {
                $this->renderPage('<div class="error">Vous devez être connecté pour accéder à cette fonctionnalité</div>');
                return;
            }
        }

        $action = match ($this->action) {
            'playlist' => new DisplayPlaylistAction(),
            'add-playlist' => new AddPlaylistAction(),
            'add-track' => new AddPodcastTrackAction(),
            'login' => new LoginAction(),
            'register' => new RegisterAction(),
            'logout' => new LogoutAction(),
            'admin' => new AdminAction(),
            default => new DefaultAction(),
        };
        try {
            $this->renderPage($action->execute());
        } catch (\Exception $e) {
            $this->renderPage('<div class="error">Error: ' . $e->getMessage() . '</div>');
        }
    }

    private function renderPage(string $html): void {
        echo "$html";
    }
}