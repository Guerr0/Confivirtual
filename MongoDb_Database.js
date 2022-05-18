connection=new Mongo()
db=connection.getDB("LogDB");
db.createCollection("Log");

