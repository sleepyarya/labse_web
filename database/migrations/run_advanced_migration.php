<?php
/**
 * Migration Script: Advanced Features (Materialized Views & Stored Procedures)
 * Database: LABSE
 * Created: 2025-12-03
 * 
 * Script ini akan menjalankan migration untuk:
 * - Materialized Views (5 views)
 * - Stored Procedures & Functions (14 functions)
 * - Trigger Functions & Triggers (3 functions, 5 triggers)
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '5433');
define('DB_NAME', 'labse');
define('DB_USER', 'postgres');
define('DB_PASS', '12345678');

// Color output for terminal
class ConsoleColor {
    public static function success($text) {
        return "\033[0;32m✓ $text\033[0m\n";
    }
    
    public static function error($text) {
        return "\033[0;31m✗ $text\033[0m\n";
    }
    
    public static function info($text) {
        return "\033[0;36mℹ $text\033[0m\n";
    }
    
    public static function warning($text) {
        return "\033[0;33m⚠ $text\033[0m\n";
    }
    
    public static function header($text) {
        return "\033[1;35m" . str_repeat("=", 60) . "\n$text\n" . str_repeat("=", 60) . "\033[0m\n";
    }
}

// Migration class
class AdvancedMigration {
    private $conn;
    private $log = array();
    private $errors = array();
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        echo ConsoleColor::header("DATABASE CONNECTION");
        echo ConsoleColor::info("Connecting to database...");
        
        $conn_string = sprintf(
            "host=%s port=%s dbname=%s user=%s password=%s",
            DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
        );
        
        $this->conn = @pg_connect($conn_string);
        
        if (!$this->conn) {
            echo ConsoleColor::error("Connection failed: " . pg_last_error());
            exit(1);
        }
        
        echo ConsoleColor::success("Connected to database: " . DB_NAME);
        echo "\n";
    }
    
    public function run() {
        $start_time = microtime(true);
        
        echo ConsoleColor::header("ADVANCED FEATURES MIGRATION");
        echo ConsoleColor::info("Starting migration at: " . date('Y-m-d H:i:s'));
        echo "\n";
        
        // Run migrations in order
        $this->runMaterializedViews();
        $this->runStoredProcedures();
        $this->runTriggerFunctions();
        
        // Refresh views
        $this->refreshMaterializedViews();
        
        // Verification
        $this->verify();
        
        $end_time = microtime(true);
        $duration = round($end_time - $start_time, 2);
        
        // Summary
        $this->printSummary($duration);
    }
    
    private function runMaterializedViews() {
        echo ConsoleColor::header("STEP 1: MATERIALIZED VIEWS");
        $sql_file = __DIR__ . '/../create_materialized_views.sql';
        $this->executeSqlFile($sql_file, 'Materialized Views');
    }
    
    private function runStoredProcedures() {
        echo ConsoleColor::header("STEP 2: STORED PROCEDURES & FUNCTIONS");
        $sql_file = __DIR__ . '/../create_stored_procedures.sql';
        $this->executeSqlFile($sql_file, 'Stored Procedures');
    }
    
    private function runTriggerFunctions() {
        echo ConsoleColor::header("STEP 3: TRIGGER FUNCTIONS");
        $sql_file = __DIR__ . '/../create_trigger_functions.sql';
        $this->executeSqlFile($sql_file, 'Trigger Functions');
    }
    
    private function refreshMaterializedViews() {
        echo ConsoleColor::header("STEP 4: REFRESH MATERIALIZED VIEWS");
        echo ConsoleColor::info("Refreshing all materialized views...");
        
        $views = array(
            'mv_dashboard_statistics',
            'mv_personil_contributions',
            'mv_recent_activities',
            'mv_yearly_research_summary',
            'mv_popular_content'
        );
        
        foreach ($views as $view) {
            $result = @pg_query($this->conn, "REFRESH MATERIALIZED VIEW $view");
            
            if ($result) {
                echo ConsoleColor::success("Refreshed: $view");
                $this->log[] = "Refreshed materialized view: $view";
            } else {
                echo ConsoleColor::warning("Could not refresh: $view (might not exist yet)");
            }
        }
        
        echo "\n";
    }
    
    private function executeSqlFile($file_path, $name) {
        if (!file_exists($file_path)) {
            echo ConsoleColor::error("File not found: $file_path");
            $this->errors[] = "File not found: $file_path";
            return false;
        }
        
        echo ConsoleColor::info("Reading file: " . basename($file_path));
        $sql = file_get_contents($file_path);
        
        echo ConsoleColor::info("Executing SQL...");
        
        // Begin transaction
        pg_query($this->conn, 'BEGIN');
        
        $result = @pg_query($this->conn, $sql);
        
        if ($result) {
            pg_query($this->conn, 'COMMIT');
            echo ConsoleColor::success("$name created successfully!");
            $this->log[] = "$name migration completed";
            
            // Show result if available
            $row = @pg_fetch_assoc($result);
            if ($row && isset($row['status'])) {
                echo ConsoleColor::info("Result: " . $row['status']);
            }
        } else {
            pg_query($this->conn, 'ROLLBACK');
            $error = pg_last_error($this->conn);
            echo ConsoleColor::error("Error executing $name: $error");
            $this->errors[] = "Error in $name: $error";
        }
        
        echo "\n";
        return $result ? true : false;
    }
    
    private function verify() {
        echo ConsoleColor::header("VERIFICATION");
        
        // Check materialized views
        echo ConsoleColor::info("Checking materialized views...");
        $query = "SELECT matviewname FROM pg_matviews WHERE schemaname = 'public' ORDER BY matviewname";
        $result = pg_query($this->conn, $query);
        
        $count = 0;
        while ($row = pg_fetch_assoc($result)) {
            echo ConsoleColor::success("Found: " . $row['matviewname']);
            $count++;
        }
        echo ConsoleColor::info("Total materialized views: $count");
        echo "\n";
        
        // Check functions
        echo ConsoleColor::info("Checking custom functions...");
        $query = "SELECT routine_name, routine_type 
                  FROM information_schema.routines 
                  WHERE routine_schema = 'public' 
                    AND (routine_name LIKE 'sp_%' OR routine_name LIKE 'fn_%' OR routine_name LIKE 'trigger_%')
                  ORDER BY routine_name";
        $result = pg_query($this->conn, $query);
        
        $count = 0;
        while ($row = pg_fetch_assoc($result)) {
            echo ConsoleColor::success("Found: " . $row['routine_name'] . " (" . $row['routine_type'] . ")");
            $count++;
        }
        echo ConsoleColor::info("Total custom functions: $count");
        echo "\n";
        
        // Check triggers
        echo ConsoleColor::info("Checking triggers...");
        $query = "SELECT trigger_name, event_object_table 
                  FROM information_schema.triggers 
                  WHERE trigger_schema = 'public'
                  ORDER BY trigger_name";
        $result = pg_query($this->conn, $query);
        
        $count = 0;
        while ($row = pg_fetch_assoc($result)) {
            echo ConsoleColor::success("Found: " . $row['trigger_name'] . " on " . $row['event_object_table']);
            $count++;
        }
        echo ConsoleColor::info("Total triggers: $count");
        echo "\n";
    }
    
    private function printSummary($duration) {
        echo ConsoleColor::header("MIGRATION SUMMARY");
        
        echo ConsoleColor::info("Total duration: {$duration}s");
        echo ConsoleColor::info("Success logs: " . count($this->log));
        
        if (empty($this->errors)) {
            echo ConsoleColor::success("Migration completed successfully! No errors.");
        } else {
            echo ConsoleColor::error("Migration completed with " . count($this->errors) . " error(s):");
            foreach ($this->errors as $error) {
                echo ConsoleColor::error("  - $error");
            }
        }
        
        echo "\n";
        echo ConsoleColor::header("NEXT STEPS");
        echo ConsoleColor::info("1. Test the materialized views: SELECT * FROM mv_dashboard_statistics;");
        echo ConsoleColor::info("2. Test stored procedures: SELECT sp_get_dashboard_stats();");
        echo ConsoleColor::info("3. Setup cron job to refresh views periodically");
        echo ConsoleColor::info("4. Read documentation: database/README_ADVANCED_FEATURES.md");
        echo "\n";
        
        echo ConsoleColor::success("All done! 🎉");
    }
    
    public function __destruct() {
        if ($this->conn) {
            pg_close($this->conn);
        }
    }
}

// Run migration
try {
    $migration = new AdvancedMigration();
    $migration->run();
} catch (Exception $e) {
    echo ConsoleColor::error("Fatal error: " . $e->getMessage());
    exit(1);
}
