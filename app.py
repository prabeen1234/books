from flask import Flask, request, jsonify
import mysql.connector
import pandas as pd
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.feature_extraction.text import CountVectorizer

app = Flask(__name__)

# Define a function to calculate content-based recommendations
def get_content_based_recommendations(user_id, ratings_threshold=4):
    recommendations = []
    # Connect to the MySQL database
    conn = mysql.connector.connect(
        host='localhost',
        user='root',
        password='Milu@123',
        database='book'
    )

    cursor = conn.cursor()
    # Query to get unique genre values
    query = "SELECT DISTINCT genre FROM books"
    cursor.execute(query)
    genre_records = cursor.fetchall()

    # Create a mapping of genres to integers
    genre_mapping = {genre: idx for idx, (genre,) in enumerate(genre_records, start=1)}# Update 'genre_numeric' based on the mapping
    for genre, numeric_value in genre_mapping.items():
    # Escape single quotes in the genre name by doubling them
        genre = genre.replace("'", "''")
        update_query = f"UPDATE books SET genre_numeric = {numeric_value} WHERE genre = '{genre}'"
        cursor.execute(update_query)
        conn.commit()

    # Fetch user-rated books with ratings above the threshold
    query = f"""
        SELECT r.user_id, r.book_id, b.genre
        FROM ratings AS r
        INNER JOIN books AS b ON r.book_id = b.id
        WHERE r.user_id = {user_id} AND r.rating >= {ratings_threshold}
    """
    cursor.execute(query)
    user_rated_books = pd.DataFrame(cursor.fetchall(), columns=['user_id', 'book_id', 'genre'])

    if user_rated_books.empty:
        return recommendations

    # Convert genre to numeric values
    genre_to_numeric = {
        'fiction': 1,
        'non-fiction': 2,
        'study': 3,
        'mystery': 4,
        'romance': 5,
        'science-fiction': 6
    }
    user_rated_books['genre_numeric'] = user_rated_books['genre'].map(genre_to_numeric)
    
    # Get the user's highly rated genres
    highly_rated_genres = user_rated_books['genre_numeric'].tolist()
   
   # Fetch all books with the same highly rated genres
    genre_filter = ', '.join(map(str, highly_rated_genres))  # Convert to a comma-separated string
    query = f"""
    SELECT id, genre
    FROM books
    WHERE genre_numeric IN ({genre_filter})
    """
    cursor.execute(query)
    similar_genre_books = pd.DataFrame(cursor.fetchall(), columns=['book_id', 'genre'])


    if similar_genre_books.empty:
        return []

    # Create a CountVectorizer to convert genres to numerical values
    count_vectorizer = CountVectorizer()
    genre_matrix = count_vectorizer.fit_transform(similar_genre_books['genre'])

    # Calculate the cosine similarity between genres
    cosine_sim = cosine_similarity(genre_matrix, genre_matrix)

    # Create a mapping of book_id to its index in the DataFrame
    indices = pd.Series(similar_genre_books.index, index=similar_genre_books['book_id'])

    # Get the book ID of the book for which you want recommendations
    book_id = similar_genre_books.iloc[0]['book_id']

    idx = indices[book_id]
    sim_scores = list(enumerate(cosine_sim[idx]))
    sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)
    sim_scores = sim_scores[1:11]
    recommendations = [similar_genre_books['book_id'].iloc[i[0]] for i in sim_scores]
    return recommendations
    cursor.close()
    conn.close()

@app.route('/recommendations', methods=['POST'])
def get_recommendations():
    data = request.get_json()
    user_id = data.get('user_id')
    ratings_threshold = data.get('ratings_threshold', 4)

    if not user_id:
        return jsonify({'error': 'Please provide a user_id'}), 400


    recommendations = get_content_based_recommendations(user_id, ratings_threshold)
    recommendations = [int(book_id) for book_id in recommendations]
    print(f"Recommendations for user_id {user_id}: {recommendations}")

    return jsonify({'recommendations': recommendations})

if __name__ == '__main__':
    app.run(debug=True)
