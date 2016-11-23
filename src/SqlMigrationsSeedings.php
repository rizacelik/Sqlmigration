<?php namespace Sqlmigration\MigrationsSeedings;

use DB;

class SqlMigrationsSeedings
{
    private $ignore = array('migrations');
    private $schema = array();
    private $up   = "";
    private $down = "";
	
    private function dbName()
    {
		$db = DB::select('SELECT DATABASE()');
		$db = array_values(json_decode(json_encode($db[0]),true));
		return $db[0];
	}
	
    private function getTables()
    {
		return DB::table('INFORMATION_SCHEMA.TABLES')->whereRaw('TABLE_SCHEMA = Database()')->whereRaw('TABLE_TYPE = \'BASE TABLE\'')->select('table_name')->get();
    }
 
    private function getTableDescribes($table)
    {
        return DB::table('information_schema.columns')
                ->whereRaw('TABLE_SCHEMA = Database()')
                ->where('table_name', '=', $table)
                ->get();
    }
 
    private function getForeignTables()
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
                ->whereRaw('CONSTRAINT_SCHEMA = Database()')
                ->whereRaw('REFERENCED_TABLE_SCHEMA = Database()')
                ->select('TABLE_NAME')->distinct()
                ->get();
    }
 
    private function getForeigns($table)
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
                ->whereRaw('CONSTRAINT_SCHEMA = Database()')
                ->whereRaw('REFERENCED_TABLE_SCHEMA = Database()')
                ->where('TABLE_NAME', '=', $table)
                ->select('COLUMN_NAME', 'REFERENCED_TABLE_NAME', 'REFERENCED_COLUMN_NAME')
                ->get();
    }
	
    private function getForeignsRule($table)
    {
        return DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
                ->whereRaw('CONSTRAINT_SCHEMA = Database()')
                ->whereRaw('UNIQUE_CONSTRAINT_SCHEMA = Database()')
                ->where('TABLE_NAME', '=', $table)
                ->select('UPDATE_RULE', 'DELETE_RULE')
                ->get();
    }
 
    private function compileSchema()
    {
        $upSchema = "";
        $downSchema = "";
        $newSchema = "";
        foreach ($this->schema as $name => $values) {
            if (in_array($name, $this->ignore)) {
                continue;
            }
            $upSchema .= "
//
// NOTE -- {$name}
// --------------------------------------------------
 
{$values['up']}";
            if ( $values['down'] !== "" ) {
            $downSchema .= "
{$values['down']}";
            }
        }
 
        $schema = "<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// NOTE Migration Created: " . date("Y-m-d H:i:s") . "
// --------------------------------------------------
 
class Create" . str_replace('_', '', ucfirst($this->dbName())) . "Database  extends Migration{

//
// NOTE - Make changes to the database.
// --------------------------------------------------
 
public function up()
{
" . $upSchema . "
" . $this->up . "
}
 
//
// NOTE - Revert the changes to the database.
// --------------------------------------------------
 
public function down()
{
" . $downSchema . "
" . $this->down . "
}
}";
 
        return $schema;
    }
 
    public function ignore($tables)
    {
        $this->ignore = array_merge($tables, $this->ignore);
        return $this;
    }
 
    public function write()
    {
        $schema = $this->compileSchema();
        $filename = date('Y_m_d_His') . "_create_" . $this->dbName() . "_database.php";
        $path = app()->databasePath().'/migrations/';
        file_put_contents($path.$filename, $schema);
    }
 
    public function convert()
    {
	$downStack = array();
        $tables = $this->getTables();

        foreach ($tables as $key => $value) {

            if (in_array($value->table_name, $this->ignore)) {
                continue;
            }
 
            $downStack[] = $value->table_name;
			
	    $result = DB::table($value->table_name)
                         ->limit(10)
                         ->get()->toArray();
				
	   $result = array_map(function ($value) {
		return (array)$value;
	   }, $result);
			
	    $export = var_export($result, true);
			
            $down = "Schema::drop('{$value->table_name}');";
	    $has  = "if (!Schema::hasTable('{$value->table_name}')) {". PHP_EOL;
            $up   = $has."Schema::create('{$value->table_name}', function(Blueprint $" . "table) {" . PHP_EOL;
	    $insert = $result ? "DB::table('{$value->table_name}')->insert(" . PHP_EOL . $export .");" . PHP_EOL : '';

            $tableDescribes = $this->getTableDescribes($value->table_name);

            foreach ($tableDescribes as $values) {

                $method = "";
                $para   = strpos($values->COLUMN_TYPE, '(');
                $type   = $para > -1 ? substr($values->COLUMN_TYPE, 0, $para) : $values->COLUMN_TYPE;

                $numbers  = "";
                $nullable = $values->IS_NULLABLE == "NO" ? "" : "->nullable()";
				
		$time_stamp = strpos($values->COLUMN_DEFAULT, "TIMESTAMP") === false ? false : true;
		$on_time_stamp = strpos($values->EXTRA, "TIMESTAMP") === false ? false : true;
		if($time_stamp && $on_time_stamp){
		    $default  = empty($values->COLUMN_DEFAULT) ? "" : "->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))";
		}elseif($time_stamp){
		    $default  = empty($values->COLUMN_DEFAULT) ? "" : "->default(DB::raw('CURRENT_TIMESTAMP'))";
		}else{
		     $default  = empty($values->COLUMN_DEFAULT) ? "" : "->default('{$values->COLUMN_DEFAULT}')";
		}
				               
                $unsigneds = strpos($values->COLUMN_TYPE, "unsigned") === false ? false : true;
				$unsigned = '';
                $unique   = $values->COLUMN_KEY == 'UNI' ? "->unique()" : "";
                $choices  = '';

                switch ($values->DATA_TYPE) {
                    case 'enum':
                        $method = 'enum';
                        $choices = preg_replace('/enum/', 'array', $values->COLUMN_TYPE);
                        $choices = ", $choices";
                        break;
                    case 'int' :
                        $method = $unsigneds ? 'unsignedInteger' : 'integer';
                        break;
                    case 'bigint' :
			$method = $unsigneds ? 'unsignedBigInteger' : 'bigInteger';
                        break;
                    case 'medium' :
			$method = $unsigneds ? 'unsignedMediumInteger' : 'mediumInteger';
                        break;
                    case 'samllint' :
			$method = $unsigneds ? 'unsignedSmallInteger' : 'smallInteger';
                        break;
                    case 'char' :
                    case 'varchar' :
                        $para = strpos($values->COLUMN_TYPE, '(');
                        $numbers = ", " . substr($values->COLUMN_TYPE, $para + 1, -1);
                        $method = 'string';
                        break;
                    case 'float' :
                        $method = 'float';
                        break;
                    case 'decimal' :
                        $para = strpos($values->COLUMN_TYPE, '(');
                        $numbers = ", " . substr($values->COLUMN_TYPE, $para + 1, -1);
                        $method = 'decimal';
                        break;
                    case 'tinyint' :
                        if ($values->COLUMN_TYPE == 'tinyint(1)') {
                            $method = 'boolean';
                        } else {
			    $method = $unsigneds ? 'unsignedTinyInteger' : 'tinyInteger';
                        }
                        break;
                    case 'date':
                        $method = 'date';
                        break;
                    case 'timestamp' :
                        $method = 'timestamp';
                        break;
                    case 'datetime' :
                        $method = 'dateTime';
                        break;
                    case 'mediumtext' :
                        $method = 'mediumtext';
                        break;
                    case 'text' :
                        $method = 'text';
                        break;
                }
                if ($values->COLUMN_KEY == 'PRI') {
                    $method = 'increments';
		    $unsigned = $unsigneds ? '->unsigned()' : '';
                }

                $up .= "\t$" . "table->{$method}('{$values->COLUMN_NAME}'{$choices}{$numbers}){$nullable}{$default}{$unsigned}{$unique};".PHP_EOL;
            }
 
            $up .= " });" . PHP_EOL . PHP_EOL;
            $if = "\r\n}else{\r\n   echo \"{$value->table_name} table already exist.\\r\\n\";\r\n}". PHP_EOL . PHP_EOL;
			
            $this->schema[$value->table_name] = array(
                'up'   => $up . $insert . $if,
                'down' => $down
            );

        }

        $tableForeigns = $this->getForeignTables();
        if (sizeof($tableForeigns) !== 0) {
            foreach ($tableForeigns as $key => $value) {
		$has  = "if (!Schema::hasTable('{$value->TABLE_NAME}')) {". PHP_EOL;
                $up = $has."Schema::table('{$value->TABLE_NAME}', function(Blueprint $" . "table) {".PHP_EOL;
				
                $foreign = $this->getForeigns($value->TABLE_NAME);
                $foreigns = $this->getForeignsRule($value->TABLE_NAME);
			
                foreach ($foreign as $k => $v) {
                    $up .= "  $" . "table->foreign('{$v->COLUMN_NAME}')" . PHP_EOL . "\t->references('{$v->REFERENCED_COLUMN_NAME}')" . PHP_EOL . "\t->on('{$v->REFERENCED_TABLE_NAME}')".PHP_EOL;
			if(count($foreigns) > 0){
				$up .= "\t->onDelete('{$foreigns[0]->DELETE_RULE}')".PHP_EOL;
				$up .= "\t->onUpdate('{$foreigns[0]->UPDATE_RULE}');".PHP_EOL;
			}
                }
				
                $up .= " });\r\n}" . PHP_EOL . PHP_EOL;
                $this->schema[$value->TABLE_NAME . '_foreign'] = array(
                    'up'   => $up,
                    'down' => ( ! in_array($value->TABLE_NAME, $downStack) ) ? $down : "",
                );
            }
        }
 
        return $this;
    }
}
