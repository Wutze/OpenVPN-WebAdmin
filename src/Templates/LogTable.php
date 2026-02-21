<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020/2025 OpenVPN-WebAdmin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * @author      Wutze
 * @copyright   2025 OpenVPN-WebAdmin
 * @link        https://github.com/Wutze/OpenVPN-WebAdmin
 * @see         Internal Documentation ~/doc/
 * @version     2.0.0
 */
$_id = \Lang::get('_LOG_COL_ID');
$_user = \Lang::get('_LOG_COL_USER');
$_fromIp = \Lang::get('_LOG_COL_FROM_IP');
$_fromPort = \Lang::get('_LOG_COL_FROM_PORT');
$_intIp = \Lang::get('_LOG_COL_INT_IP');
$_intPort = \Lang::get('_LOG_COL_INT_PORT');
$_rec = \Lang::get('_LOG_COL_REC');
$_sent = \Lang::get('_LOG_COL_SENT');
$_start = \Lang::get('_LOG_COL_START');
$_end = \Lang::get('_LOG_COL_END');

return <<<HTML
            <table class="table-sm"
              id="log-table"
              data-toggle="table"
              data-side-pagination="server"
              data-pagination="true"
              data-search="true"
              data-detail-formatter="logdetails"
              data-search-time-out="1000"
              data-url="?op=data&amp;select=log">
              <thead class="thead-dark">
                <tr>
                   <th data-field="log_id" >{$_id}</th>
                   <th data-field="user_name" >{$_user}</th>
                   <th data-field="log_trusted_ip">{$_fromIp}</th>
                   <th data-field="log_trusted_port" >{$_fromPort}</th>
                   <th data-field="log_remote_ip">{$_intIp}</th>
                   <th data-field="log_remote_port">{$_intPort}</th>
                   <th data-field="log_received" data-sortable="true">{$_rec}</th>
                   <th data-field="log_send" data-sortable="true">{$_sent}</th>
                   <th data-field="log_start_time">{$_start}</th>
                   <th data-field="log_end_time">{$_end}</th>
                </tr>
              </thead>
            </table>
HTML;
