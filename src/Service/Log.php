<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Impl\Service;

use Fusio\Impl\Authorization\TokenGenerator;
use Fusio\Impl\Backend\Table\Log as TableLog;
use Fusio\Impl\Backend\Table\Log\QueryFilter;
use PSX\Data\ResultSet;
use PSX\DateTime;
use PSX\Http\Exception as StatusCode;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * Log
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Log
{
    protected $logTable;

    public function __construct(TableLog $logTable)
    {
        $this->logTable = $logTable;
    }

    public function getAll($startIndex = null, QueryFilter $filter)
    {
        $condition = $filter->getCondition();

        $this->logTable->setRestrictedFields(['header', 'body']);

        return new ResultSet(
            $this->logTable->getCount($condition),
            $startIndex,
            16,
            $this->logTable->getAll($startIndex, 16, 'id', Sql::SORT_DESC, $condition)
        );
    }

    public function get($logId)
    {
        $log = $this->logTable->get($logId);

        if (!empty($log)) {
            // append errors
            $log['errors'] = $this->logTable->getErrors($log['id']);

            return $log;
        } else {
            throw new StatusCode\NotFoundException('Could not find log');
        }
    }
}
