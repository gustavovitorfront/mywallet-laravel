<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateProceduresTriggersTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure_insert = '
            CREATE PROCEDURE insert_transaction (
                IN user_id INT,
                IN description VARCHAR(150),
                IN value double(8,2),
                IN category VARCHAR(150),
                IN payed tinyint(1),
                IN expend tinyint(1),
                IN created_at timestamp,
                IN updated_at timestamp
            )
            BEGIN
                INSERT INTO transactions (user_id, description, value, category, payed, expend, created_at, updated_at)
                VALUES (user_id, description, value, category, payed, expend, created_at, updated_at );
            END
        ';

        DB::unprepared($procedure_insert);

        $procedure_update = '
            CREATE PROCEDURE update_transaction (
                IN id INT,
                IN user_id INT,
                IN description VARCHAR(150),
                IN value double(8,2),
                IN category VARCHAR(150),
                IN payed tinyint(1),
                IN expend tinyint(1),
                IN created_at timestamp,
                IN updated_at timestamp
            )
            BEGIN
                UPDATE transactions SET
                    user_id = user_id,
                    description = description,
                    value = value,
                    category = category,
                    payed = payed,
                    expend = expend,
                    updated_at = updated_at
                WHERE
                    id = id;
            END
        ';

        DB::unprepared($procedure_update);

        $trigger_update_user_balance_insert = '
            CREATE TRIGGER update_user_balance_insert AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                DECLARE user_balance DOUBLE(8,2);
                        
                -- Calcular o novo saldo do usuário com base na transação
                SELECT balance INTO user_balance FROM users WHERE id = NEW.user_id;
            
                IF NEW.expend = 1 THEN
                    -- A transação é uma despesa, subtrair seu valor do saldo do usuário se a transação foi paga
                    IF NEW.payed = 1 THEN
                        SET user_balance = user_balance - NEW.value;
                    END IF;
                ELSE
                    -- A transação é uma receita, adicionar seu valor ao saldo do usuário se a transação foi paga
                    IF NEW.payed = 1 THEN
                        SET user_balance = user_balance + NEW.value;
                    END IF;
                END IF;
            
                -- Atualizar o saldo do usuário na tabela users
                UPDATE users SET balance = user_balance WHERE id = NEW.user_id;
            END
        ';

        DB::unprepared($trigger_update_user_balance_insert);

        $trigger_update_user_balance_update = '
            CREATE TRIGGER update_user_balance_update
            AFTER UPDATE ON transactions
            FOR EACH ROW
            BEGIN
                DECLARE user_balance DOUBLE(8,2);
            
                -- Calcular o novo saldo do usuário com base na transação
                SELECT balance INTO user_balance FROM users WHERE id = NEW.user_id;
            
                IF NEW.expend = 1 THEN
                    -- A transação é uma despesa, subtrair seu valor do saldo do usuário se a transação foi paga
                    IF NEW.payed = 1 THEN
                        SET user_balance = user_balance - NEW.value;
                    END IF;
                ELSE
                    -- A transação é uma receita, adicionar seu valor ao saldo do usuário se a transação foi paga
                    IF NEW.payed = 1 THEN
                        SET user_balance = user_balance + NEW.value;
                    END IF;
                END IF;
            
                -- Atualizar o saldo do usuário na tabela users
                UPDATE users SET balance = user_balance WHERE id = NEW.user_id;
            END
        ';

        DB::unprepared($trigger_update_user_balance_update);

        $trigger_update_user_balance_delete = '
            CREATE TRIGGER update_user_balance_delete
            BEFORE DELETE ON transactions
            FOR EACH ROW
            BEGIN
                DECLARE user_balance DOUBLE(8,2);
                
                SELECT balance INTO user_balance FROM users WHERE id = OLD.user_id;

                -- Atualizar o saldo do usuário
                IF OLD.expend = 1 THEN
                    IF OLD.payed = 1 THEN
                        SET user_balance = user_balance + OLD.value;
                    END IF;
                ELSE
                    IF OLD.payed = 1 THEN
                        SET user_balance = user_balance - OLD.value;
                    END IF;
                END IF;

                UPDATE users SET balance = user_balance WHERE id = OLD.user_id;
            END
        ';

        DB::unprepared($trigger_update_user_balance_delete);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS insert_transaction');
        DB::unprepared('DROP PROCEDURE IF EXISTS update_transaction');
        DB::unprepared('DROP TRIGGER `update_user_balance_insert`');
        DB::unprepared('DROP TRIGGER `update_user_balance_update`');
        DB::unprepared('DROP TRIGGER `update_user_balance_delete`');
    }
}
