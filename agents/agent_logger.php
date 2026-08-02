<?php
/**
 * AgriSync — AI Agent Activity Logger
 * Logs every step of AI agent execution (prompts, queries, evaluations, decisions) to agent_logs table.
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}
require_once __DIR__ . '/../config/database.php';

class AgentLogger {
    /**
     * Record a step in the AI agent lifecycle
     *
     * @param string $agentType e.g., 'broker', 'demand_predictor'
     * @param string $actionStep Descriptive name of the action step
     * @param int|null $orderId Associated order ID if applicable
     * @param array $logData Detailed payload, prompt, response, metrics
     * @return int|null Inserted log ID or null on failure
     */
    public static function log(string $agentType, string $actionStep, ?int $orderId = null, array $logData = [], ?PDO $customDb = null): ?int {
        try {
            $db = $customDb ?? getDbConnection();
            $stmt = $db->prepare("
                INSERT INTO agent_logs (agent_type, order_id, action_step, log_data, created_at)
                VALUES (:agent_type, :order_id, :action_step, :log_data, NOW())
            ");

            $jsonLogData = !empty($logData) ? json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;

            $stmt->bindValue(':agent_type', $agentType, PDO::PARAM_STR);
            $stmt->bindValue(':order_id', $orderId, $orderId !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(':action_step', $actionStep, PDO::PARAM_STR);
            $stmt->bindValue(':log_data', $jsonLogData, $jsonLogData !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);

            $stmt->execute();
            return (int) $db->lastInsertId();
        } catch (Throwable $e) {
            // Never crash agent operations if logging fails; record to error log
            error_log('AgentLogger failure: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch logs for a specific order (for transparency displays)
     *
     * @param int $orderId
     * @return array
     */
    public static function getLogsForOrder(int $orderId): array {
        try {
            $db = getDbConnection();
            $stmt = $db->prepare("
                SELECT id, agent_type, order_id, action_step, log_data, created_at 
                FROM agent_logs 
                WHERE order_id = :order_id 
                ORDER BY id ASC
            ");
            $stmt->execute([':order_id' => $orderId]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($logs as &$log) {
                if (!empty($log['log_data']) && is_string($log['log_data'])) {
                    $log['log_data'] = json_decode($log['log_data'], true);
                }
            }
            return $logs;
        } catch (Throwable $e) {
            error_log('AgentLogger::getLogsForOrder error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch recent agent logs across the platform (for Admin Monitor)
     *
     * @param int $limit
     * @param string|null $agentType
     * @return array
     */
    public static function getRecentLogs(int $limit = 50, ?string $agentType = null): array {
        try {
            $db = getDbConnection();
            $sql = "SELECT al.*, u.name as business_name, o.crop_type, o.quantity_kg
                    FROM agent_logs al
                    LEFT JOIN order_requests o ON al.order_id = o.id
                    LEFT JOIN users u ON o.business_id = u.id";

            if ($agentType !== null) {
                $sql .= " WHERE al.agent_type = :agent_type";
            }

            $sql .= " ORDER BY al.id DESC LIMIT :limit";

            $stmt = $db->prepare($sql);
            if ($agentType !== null) {
                $stmt->bindValue(':agent_type', $agentType, PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->execute();

            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($logs as &$log) {
                if (!empty($log['log_data']) && is_string($log['log_data'])) {
                    $log['log_data'] = json_decode($log['log_data'], true);
                }
            }
            return $logs;
        } catch (Throwable $e) {
            error_log('AgentLogger::getRecentLogs error: ' . $e->getMessage());
            return [];
        }
    }
}
