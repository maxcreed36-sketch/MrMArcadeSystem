-- Create the users table
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    passcode TEXT NOT NULL,
    current_score INTEGER DEFAULT 0,
    history TEXT DEFAULT ''
);

-- Create a trigger to automatically update current_score from history
CREATE TRIGGER update_current_score 
AFTER UPDATE OF history ON users
FOR EACH ROW
BEGIN
    UPDATE users 
    SET current_score = (
        SELECT SUM(value) 
        FROM json_each('[' || REPLACE(NEW.history, ',', ',') || ']')
        WHERE json_valid('[' || REPLACE(NEW.history, ',', ',') || ']')
    )
    WHERE id = NEW.id;
END;

-- Insert some empty sample data
INSERT INTO users (username, passcode, current_score, history) 
VALUES 
('user1', 'pass123', 0, ''),
('user2', 'secret456', 0, ''),
('user3', 'mypass789', 0, '');

-- Alternative method: Create a function to calculate score from history
CREATE TEMPORARY VIEW user_scores AS
SELECT 
    id,
    username,
    passcode,
    (
        SELECT SUM(value) 
        FROM json_each('[' || REPLACE(history, ',', ',') || ']')
        WHERE json_valid('[' || REPLACE(history, ',', ',') || ']')
    ) AS calculated_score,
    history
FROM users;

-- Query to see all data
SELECT * FROM users;

-- Query to see calculated scores (alternative method)
SELECT * FROM user_scores;