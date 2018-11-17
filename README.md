# Profile-Personalization-Elasticsearch
A personalization on Elasticsearch by using Google+ profile as a personalization data

Stack:
- Elasticsearch 6.1.2
- PHP 5.6
- Bootstrap
- elasticsearch-php

Some description: 

- An implementation of Elasticsearch with Google+ API.
- The user will need to login the system using their Google account.
- then the user data will be sent to our system and stored to session.
- the data are also automatically indexed into elasticsearch index.
- yes, you can use the data in the session for personalization. 
- I indexed them cuz i need the data for testing purpose.
- The query used in this system implements synonym graph token filter that used at search time.
- So the synonym could be dynamic and support multi term synonym.
- the analyzer used are indonesian language analyzer in different sub fields
- by doing this we keep the original content with default analyzer, 
- and then the other subfields using remove stopwords, and stemmer so we have a wide range of contents to search into.
- In this case i used news articles data to search on.
