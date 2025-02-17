<?php

namespace App\Observers;

use App\Model\Transaction;

class TransactionsObserver
{
    /**
     * Listen to the Transaction deleting event.
     *
     * @param  Transaction  $transaction
     * @return void
     */
    public function deleting(Transaction $transaction)
    {
        // removes invoice along with transaction
        if ($transaction->invoice()) {
            $transaction->invoice()->delete();
        }
    }

    /**
     * Listen to the Transaction created event.
     * @param Transaction $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {
    }

    /**
     * Listen to the Transaction updated event.
     * @param Transaction $transaction
     * @return void
     */
    public function updating(Transaction $transaction)
    {
    }
}
