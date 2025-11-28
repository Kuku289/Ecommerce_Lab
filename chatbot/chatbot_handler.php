<?php
/**
 * Chatbot Handler - Processes messages and returns responses
 */

require_once 'chatbot_config.php';

class ChatbotHandler {
    
    /**
     * Process user message and return response
     */
    public static function processMessage($message, $customer_id = null) {
        $message = strtolower(trim($message));
        $responses = ChatbotConfig::getResponses();
        
        // Check each category for keyword matches
        foreach ($responses as $category => $data) {
            if ($category === 'default') continue;
            
            foreach ($data['keywords'] as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    // Random response from matching category
                    $response = $data['responses'][array_rand($data['responses'])];
                    
                    // Save conversation
                    self::saveConversation($message, $response, $customer_id);
                    
                    return [
                        'success' => true,
                        'response' => $response,
                        'category' => $category
                    ];
                }
            }
        }
        
        // No match found - return default response
        $defaultResponses = $responses['default']['responses'];
        $response = $defaultResponses[array_rand($defaultResponses)];
        
        self::saveConversation($message, $response, $customer_id);
        
        return [
            'success' => true,
            'response' => $response,
            'category' => 'default'
        ];
    }
    
    /**
     * Save conversation to database
     */
    private static function saveConversation($message, $response, $customer_id = null) {
        require_once('../settings/db_class.php');
        
        $db = new db_connection();
        $conn = $db->db_conn();
        
        $session_id = session_id();
        $sentiment = self::analyzeSentiment($message);
        
        $sql = "INSERT INTO chatbot_conversations (customer_id, session_id, message, response, sentiment) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $customer_id, $session_id, $message, $response, $sentiment);
        $stmt->execute();
    }
    
    /**
     * Simple sentiment analysis
     */
    private static function analyzeSentiment($message) {
        $positive_words = ['good', 'great', 'excellent', 'love', 'thank', 'amazing', 'wonderful', 'best', 'happy'];
        $negative_words = ['bad', 'terrible', 'hate', 'awful', 'worst', 'angry', 'frustrated', 'disappointed'];
        
        $message_lower = strtolower($message);
        
        foreach ($positive_words as $word) {
            if (strpos($message_lower, $word) !== false) {
                return 'Positive';
            }
        }
        
        foreach ($negative_words as $word) {
            if (strpos($message_lower, $word) !== false) {
                return 'Negative';
            }
        }
        
        return 'Neutral';
    }
    
    /**
     * Get conversation history
     */
    public static function getConversationHistory($limit = 10, $customer_id = null) {
        require_once('../settings/db_class.php');
        
        $db = new db_connection();
        $conn = $db->db_conn();
        
        if ($customer_id) {
            $sql = "SELECT * FROM chatbot_conversations WHERE customer_id = ? ORDER BY timestamp DESC LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $customer_id, $limit);
        } else {
            $session_id = session_id();
            $sql = "SELECT * FROM chatbot_conversations WHERE session_id = ? ORDER BY timestamp DESC LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $session_id, $limit);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $conversations = [];
        while ($row = $result->fetch_assoc()) {
            $conversations[] = $row;
        }
        
        return array_reverse($conversations);
    }
}
?>