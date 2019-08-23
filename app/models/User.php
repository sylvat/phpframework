<?php
namespace App\Models;

class User extends ModelBase
{

    protected static $tableName = 'user';

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(column="userId", type="integer", length=20, nullable=false)
     */
    public $userId;

    /**
     *
     * @var string
     * @Column(column="nickname", type="string", length=128, nullable=false)
     */
    public $nickname;

    /**
     *
     * @var string
     * @Column(column="avatar", type="string", length=1024, nullable=false)
     */
    public $avatar;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource(self::getTableName());
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return self::getTableName();
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return User[]|User|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return User|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
