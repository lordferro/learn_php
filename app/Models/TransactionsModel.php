<?php

declare(strict_types=1);

namespace App\Models;

use App\Model;

class TransactionsModel extends Model
{
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT into transactions (date, `check`, description, amount) values (?,?,?,?)");


        foreach($data as $transaction) {

            $formattedDate = date('Y-m-d', strtotime($transaction['date']));

            $checkValue = $transaction['check'] ? $transaction['check'] : null;

            $stmt->bindParam(1, $formattedDate);
            $stmt->bindParam(2, $checkValue, \PDO::PARAM_INT);
            $stmt->bindParam(3, $transaction['description']);
            $stmt->bindParam(4, $transaction['amount']);

            $stmt->execute();
        }
    }

    public function getAll(){
        $stmt = $this->db->prepare('SELECT * from transactions');

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
