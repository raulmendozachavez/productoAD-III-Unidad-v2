<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('detalle_pedidos')) {
            return;
        }

        $schemaRow = DB::selectOne("SELECT DATABASE() AS db");
        $dbName = $schemaRow ? $schemaRow->db : null;

        $columnRow = DB::selectOne(
            "SELECT COLUMN_TYPE AS column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'detalle_pedidos' AND COLUMN_NAME = 'id_producto'",
            [$dbName]
        );

        $columnType = $columnRow && isset($columnRow->column_type) ? $columnRow->column_type : 'int(11)';

        $fkRow = DB::selectOne(
            "SELECT CONSTRAINT_NAME AS constraint_name FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'detalle_pedidos' AND COLUMN_NAME = 'id_producto' AND REFERENCED_TABLE_NAME = 'productos'",
            [$dbName]
        );

        if ($fkRow && isset($fkRow->constraint_name)) {
            DB::statement("ALTER TABLE `detalle_pedidos` DROP FOREIGN KEY `{$fkRow->constraint_name}`");
        }

        DB::statement("ALTER TABLE `detalle_pedidos` MODIFY `id_producto` {$columnType} NULL");

        Schema::table('detalle_pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('detalle_pedidos', 'producto_nombre')) {
                $table->string('producto_nombre', 100)->nullable()->after('id_producto');
            }
            if (!Schema::hasColumn('detalle_pedidos', 'producto_categoria')) {
                $table->string('producto_categoria', 50)->nullable()->after('producto_nombre');
            }
            if (!Schema::hasColumn('detalle_pedidos', 'producto_imagen')) {
                $table->string('producto_imagen', 255)->nullable()->after('producto_categoria');
            }
        });

        DB::statement(
            "UPDATE `detalle_pedidos` dp\n".
            "LEFT JOIN `productos` p ON dp.`id_producto` = p.`id_producto`\n".
            "SET\n".
            "  dp.`producto_nombre` = COALESCE(dp.`producto_nombre`, p.`nombre`),\n".
            "  dp.`producto_categoria` = COALESCE(dp.`producto_categoria`, p.`categoria`),\n".
            "  dp.`producto_imagen` = COALESCE(dp.`producto_imagen`, p.`imagen`)"
        );

        DB::statement(
            "ALTER TABLE `detalle_pedidos`\n".
            "ADD CONSTRAINT `detalle_pedidos_id_producto_setnull`\n".
            "FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)\n".
            "ON DELETE SET NULL"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('detalle_pedidos')) {
            return;
        }

        $schemaRow = DB::selectOne("SELECT DATABASE() AS db");
        $dbName = $schemaRow ? $schemaRow->db : null;

        $fkRow = DB::selectOne(
            "SELECT CONSTRAINT_NAME AS constraint_name FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'detalle_pedidos' AND COLUMN_NAME = 'id_producto' AND REFERENCED_TABLE_NAME = 'productos'",
            [$dbName]
        );

        if ($fkRow && isset($fkRow->constraint_name)) {
            DB::statement("ALTER TABLE `detalle_pedidos` DROP FOREIGN KEY `{$fkRow->constraint_name}`");
        }

        Schema::table('detalle_pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('detalle_pedidos', 'producto_imagen')) {
                $table->dropColumn('producto_imagen');
            }
            if (Schema::hasColumn('detalle_pedidos', 'producto_categoria')) {
                $table->dropColumn('producto_categoria');
            }
            if (Schema::hasColumn('detalle_pedidos', 'producto_nombre')) {
                $table->dropColumn('producto_nombre');
            }
        });

        $columnRow = DB::selectOne(
            "SELECT COLUMN_TYPE AS column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'detalle_pedidos' AND COLUMN_NAME = 'id_producto'",
            [$dbName]
        );
        $columnType = $columnRow && isset($columnRow->column_type) ? $columnRow->column_type : 'int(11)';

        DB::statement("ALTER TABLE `detalle_pedidos` MODIFY `id_producto` {$columnType} NOT NULL");

        DB::statement(
            "ALTER TABLE `detalle_pedidos`\n".
            "ADD CONSTRAINT `detalle_pedidos_id_producto_fk`\n".
            "FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)"
        );
    }
};
