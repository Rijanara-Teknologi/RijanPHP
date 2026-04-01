<?php
namespace Teguh\Rijanphp\Modules\Blog\Models;

use Teguh02\Rijanphp\Core\Model\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [];
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;
}