<?php

namespace App\Http\Controllers\Panel\Boards;

use App\BanAppeal;
use App\Board;
use App\Http\Controllers\Panel\PanelController;
use Input;

/**
 * Lists and manages ban appeals.
 *
 * @category   Http
 *
 * @author     Joshua Moon <josh@jaw.sh>
 * @copyright  2016 Infinity Next Development Group
 * @license    http://www.gnu.org/licenses/agpl-3.0.en.html AGPL3
 *
 * @since      0.5.1
 */
class AppealsController extends PanelController
{
    const VIEW_APPEALS = 'panel.board.appeals';

    /**
     * View path for the secondary (sidebar) navigation.
     *
     * @var string
     */
    public static $navSecondary = 'nav.panel.board';

    public function getIndex(Board $board = null)
    {
        if (!$this->user->canManageAppealsAny()) {
            return abort(403);
        }

        $appeals = BanAppeal::getAppealsFor($this->user);

        return $this->view(static::VIEW_APPEALS, [
            'appeals' => $appeals,
        ]);
    }

    public function patchIndex(Board $board = null)
    {
        $approve = true;
        $appeal = Input::get('approve', false);

        if ($appeal === false) {
            $approve = false;
            $appeal = Input::get('reject', false);
        }
        if ($appeal === false) {
            return abort(404);
        }

        $appeal = BanAppeal::whereOpen()
            ->with('ban', 'ban.board')
            ->findOrFail((int) $appeal);

        if (!$this->user->canManageAppeals($appeal->ban->board)) {
            return abort(403);
        }

        $appeal->approved = $approve;
        $appeal->seen = true;
        $appeal->mod_id = $this->user->user_id;
        $appeal->save();

        return $this->getIndex($board);
    }
}
