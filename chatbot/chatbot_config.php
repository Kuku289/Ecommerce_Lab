<?php
/**
 * Chatbot Configuration
 * Configure AI responses and keywords here
 */

class ChatbotConfig {
    
    /**
     * Get all predefined responses with keywords
     */
    public static function getResponses() {
        return [
            // Greetings
            'greetings' => [
                'keywords' => ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening', 'greetings'],
                'responses' => [
                    "Hello! 👋 Welcome to BotaniQs! How can I help you today?",
                    "Hi there! 🌿 I'm BotaniBot, your wellness assistant. What would you like to know?",
                    "Hey! Welcome! Ask me about products, health tips, orders, or anything else! 😊"
                ]
            ],
            
            // General products inquiry
            'products' => [
                'keywords' => ['product', 'products', 'item', 'items', 'sell', 'selling', 'buy', 'purchase', 'available', 'stock', 'catalog', 'shop'],
                'responses' => [
                    "We offer organic seeds, essential oils, herbs, and natural supplements! 🌱 <a href='view/all_product.php'>Browse catalog</a>.",
                    "Looking for wellness products? We have seeds, oils, herbs, and more! <a href='view/all_product.php'>Shop now</a>.",
                    "We specialize in authentic wellness products! What type interests you? <a href='view/all_product.php'>View all</a>."
                ]
            ],
            
            // Essential oils
            'essential_oils' => [
                'keywords' => ['oil', 'oils', 'essential oil', 'aromatherapy', 'lavender', 'tea tree', 'eucalyptus', 'peppermint'],
                'responses' => [
                    "Our essential oils are 100% pure! 🌸 Popular: lavender, tea tree, eucalyptus. <a href='view/all_product.php'>Browse oils</a>.",
                    "Essential oils for aromatherapy and wellness! We have therapeutic grade oils. What are you looking for?"
                ]
            ],
            
            // Seeds
            'seeds' => [
                'keywords' => ['seed', 'seeds', 'chia', 'flax', 'pumpkin', 'sunflower'],
                'responses' => [
                    "Organic seeds packed with nutrients! 🌾 Chia, flax, pumpkin, sunflower. <a href='view/all_product.php'>Shop seeds</a>.",
                    "Seeds are nutritional powerhouses! Chia for omega-3, flax for fiber. What do you need?"
                ]
            ],
            
            // Herbs
            'herbs' => [
                'keywords' => ['herb', 'herbs', 'medicinal', 'moringa', 'ginger', 'turmeric', 'basil'],
                'responses' => [
                    "Authentic medicinal herbs! 🌿 Moringa, ginger, turmeric. All from verified suppliers. <a href='view/all_product.php'>View herbs</a>.",
                    "Our herbs are quality tested! Moringa for immunity, ginger for digestion. What helps you?"
                ]
            ],
            
            // Pricing
            'pricing' => [
                'keywords' => ['price', 'cost', 'expensive', 'cheap', 'affordable', 'how much', 'charges'],
                'responses' => [
                    "Competitive pricing from GH₵20! 💰 <a href='view/all_product.php'>Check catalog for specific prices</a>.",
                    "Affordable prices without compromising quality! Browse our catalog to see pricing for each product."
                ]
            ],
            
            // Shipping
            'shipping' => [
                'keywords' => ['shipping', 'delivery', 'deliver', 'ship', 'location', 'where'],
                'responses' => [
                    "We deliver across Ghana! 🚚 2-5 business days. Free delivery on orders over GH₵200!",
                    "Yes, we ship nationwide! Fast and reliable delivery to your doorstep."
                ]
            ],
            
            // Payment
            'payment' => [
                'keywords' => ['pay', 'payment', 'momo', 'mobile money', 'cash', 'card', 'bank'],
                'responses' => [
                    "We accept Mobile Money (MTN, Vodafone, AirtelTigo), bank transfers, and cash on delivery! 💳",
                    "Payment is easy! Mobile Money, bank transfer, or cash on delivery. Choose what works for you!"
                ]
            ],
            
            // Orders
            'orders' => [
                'keywords' => ['order', 'track', 'tracking', 'status', 'my order', 'purchase history'],
                'responses' => [
                    "To track your order, <a href='login/login.php'>login to your account</a> and check order history. Need help? Email botaniqs@gmail.com 📦",
                    "View all orders in your account dashboard after logging in. Having issues? Contact us!"
                ]
            ],
            
            // Health benefits
            'health' => [
                'keywords' => ['health', 'benefit', 'benefits', 'wellness', 'healthy', 'immunity', 'immune'],
                'responses' => [
                    "Great question! 💪 Our products support immunity, stress relief, heart health, digestion, and more. What's your wellness goal?",
                    "We focus on natural wellness! Products for immunity, digestion, skin health, stress management. Tell me your goals!"
                ]
            ],
            
            // Stress relief
            'stress' => [
                'keywords' => ['stress', 'anxiety', 'relax', 'relaxation', 'calm', 'sleep', 'insomnia'],
                'responses' => [
                    "For stress relief, try lavender oil (aromatherapy), chamomile (relaxation), or moringa (overall wellness)! 🧘‍♀️ <a href='view/all_product.php'>Browse stress-relief products</a>.",
                    "Stress management is important! Lavender oil in a diffuser, chamomile tea, or adaptogenic herbs help."
                ]
            ],
            
            // Skin care
            'skin' => [
                'keywords' => ['skin', 'skincare', 'face', 'acne', 'glow', 'complexion', 'beauty'],
                'responses' => [
                    "For healthy skin, try tea tree oil (antibacterial), shea butter (moisturizing), or moringa (antioxidants)! ✨ All natural and effective.",
                    "Natural skincare is best! Tea tree oil for acne, shea butter for hydration, various oils for skin health."
                ]
            ],
            
            // Weight loss
            'weight' => [
                'keywords' => ['weight', 'lose weight', 'fat', 'slim', 'slimming', 'diet', 'fitness'],
                'responses' => [
                    "For weight management: chia seeds (keeps you full), green tea (boosts metabolism), moringa (nutrients without calories)! 🏃‍♀️ Combine with healthy eating and exercise.",
                    "Natural weight support: chia seeds (fiber), flax seeds (omega-3), green tea. Remember, lifestyle changes are key!"
                ]
            ],
            
            // Verification/Trust
            'trust' => [
                'keywords' => ['authentic', 'genuine', 'fake', 'real', 'trust', 'trusted', 'verified', 'quality', 'certification'],
                'responses' => [
                    "All our products are 100% authentic from verified suppliers! ✅ Many have FDA approval and organic certifications. <a href='admin/suppliers.php'>View verified suppliers</a>.",
                    "Quality is our priority! We verify all suppliers, check certifications, and ensure authenticity. Your wellness is safe!"
                ]
            ],
            
            // Contact
            'contact' => [
                'keywords' => ['contact', 'phone', 'email', 'reach', 'call', 'message'],
                'responses' => [
                    "Contact us:<br>📧 Email: botaniqs@gmail.com<br>📱 Phone: +233 20 409 3497 or +233 59 573 4449<br>We're here to help!",
                    "Reach out anytime!<br>Email: botaniqs@gmail.com<br>Phone: +233 20 409 3497<br>We respond within 24 hours."
                ]
            ],
            
            // Account
            'account' => [
                'keywords' => ['account', 'register', 'signup', 'sign up', 'login', 'log in', 'password'],
                'responses' => [
                    "To create an account, <a href='login/register.php'>register here</a>! Already registered? <a href='login/login.php'>Login here</a> 👤",
                    "Account setup is quick! Register to track orders, save favorites, and get exclusive deals."
                ]
            ],
            
            // Cart
            'cart' => [
                'keywords' => ['cart', 'checkout', 'add to cart', 'basket'],
                'responses' => [
                    "Browse <a href='view/all_product.php'>products</a>, click 'Add to Cart', then <a href='view/checkout.php'>checkout</a> when ready! 🛒",
                    "Shopping is easy! Browse, add to cart, checkout securely. Need to <a href='login/login.php'>login first</a>?"
                ]
            ],
            
            // Hours
            'hours' => [
                'keywords' => ['hours', 'open', 'opening', 'close', 'closing', 'time', 'when'],
                'responses' => [
                    "Our online store is open 24/7! 🌙 Shop anytime. Customer support: Mon-Fri, 8am-6pm.",
                    "Shop anytime - we're always open online! Customer service responds Mon-Fri, 8am-6pm."
                ]
            ],
            
            // Returns
            'returns' => [
                'keywords' => ['return', 'refund', 'exchange', 'money back', 'dissatisfied'],
                'responses' => [
                    "We have a 7-day return policy for unopened products! 📦 Contact botaniqs@gmail.com with your order number.",
                    "Not satisfied? Returns accepted within 7 days for unopened items. Email us to process your return."
                ]
            ],
            
            // Thanks
            'thanks' => [
                'keywords' => ['thank', 'thanks', 'thank you', 'appreciate', 'grateful'],
                'responses' => [
                    "You're very welcome! 😊 Anything else I can help with?",
                    "My pleasure! Let me know if you need anything else. Happy shopping! 🌿"
                ]
            ],
            
            // Goodbye
            'goodbye' => [
                'keywords' => ['bye', 'goodbye', 'see you', 'later', 'good night'],
                'responses' => [
                    "Goodbye! 👋 Come back anytime. Stay healthy! 🌿",
                    "Take care! Reach out whenever you need help. Bye! 😊"
                ]
            ],
            
            // Default/Unknown
            'default' => [
                'keywords' => [],
                'responses' => [
                    "I'm not sure about that. 🤔 Can you rephrase? Try asking about products, shipping, payments, or wellness tips!",
                    "Hmm, I didn't quite get that. Ask me about:<br>• Products we sell<br>• Health benefits<br>• Shipping & payment<br>• Account help",
                    "I'm still learning! 🌱 Could you ask differently? Or contact us at botaniqs@gmail.com for detailed help."
                ]
            ]
        ];
    }
    
    /**
     * Get bot name
     */
    public static function getBotName() {
        return "BotaniBot";
    }
    
    /**
     * Get welcome message
     */
    public static function getWelcomeMessage() {
        return "Hello! I'm BotaniBot 🤖🌿, your wellness assistant! Ask me about products, health tips, orders, or anything else!";
    }
}
?>