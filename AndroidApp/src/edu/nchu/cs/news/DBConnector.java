// DatabaseConnector.java
// Provides easy connection and creation of UserContacts database.
package edu.nchu.cs.news;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.database.sqlite.SQLiteDatabase.CursorFactory;

public class DBConnector 
{
   // database name
   private static final String db_name = "nchucsnews";
   private SQLiteDatabase db; // database object
   private DatabaseOpenHelper dbOpenHelper; // database helper

   // public constructor for DatabaseConnector
   public DBConnector(Context context)
   {
      // create a new DatabaseOpenHelper
	   dbOpenHelper = new DatabaseOpenHelper(context, db_name, null, 1);
   } // end DatabaseConnector constructor

   // open the database connection
   public void open() throws SQLException 
   {
      // create or open a database for reading/writing
	   db = dbOpenHelper.getWritableDatabase();
   } // end method open

   // close the database connection
   public void close() 
   {
      if (db != null)
    	  db.close(); // close the database connection
   } // end method close

   // inserts a new contact in the database
   public void systemSet( String name, String value ) 
   {
      ContentValues newContact = new ContentValues();
      /*
      newContact.put("name", name);
      newContact.put("email", email);
      newContact.put("phone", phone);
      newContact.put("street", state);
      newContact.put("city", city);
      */

      open(); // open the database
      db.insert("contacts", null, newContact);
      close(); // close the database
   } // end method insertContact

   // inserts a new contact in the database
   public void updateContact(long id, String name, String email, 
      String phone, String state, String city) 
   {
      ContentValues editContact = new ContentValues();
      editContact.put("name", name);
      editContact.put("email", email);
      editContact.put("phone", phone);
      editContact.put("street", state);
      editContact.put("city", city);

      open(); // open the database
      db.update("contacts", editContact, "_id=" + id, null);
      close(); // close the database
   } // end method updateContact

   // return a Cursor with all contact information in the database
   public Cursor getAllContacts() 
   {
      return db.query("contacts", new String[] {"_id", "name"}, 
         null, null, null, null, "name");
   } // end method getAllContacts

   // get a Cursor containing all information about the contact specified
   // by the given id
   public Cursor getOneContact(long id) 
   {
      return db.query(
         "contacts", null, "_id=" + id, null, null, null, null);
   } // end method getOnContact

   // delete the contact specified by the given String name
   public void deleteContact(long id) 
   {
      open(); // open the database
      db.delete("contacts", "_id=" + id, null);
      close(); // close the database
   } // end method deleteContact
   
   private class DatabaseOpenHelper extends SQLiteOpenHelper 
   {
      // public constructor
      public DatabaseOpenHelper(Context context, String name,
         CursorFactory factory, int version) 
      {
         super(context, name, factory, version);
      } // end DatabaseOpenHelper constructor

      // creates the contacts table when the database is created
      @Override
      public void onCreate(SQLiteDatabase db) 
      {
         // query to create a new table named contacts
         String createQuery = "CREATE TABLE `system` ( `_id` INT(10) NOT NULL AUTO_INCREMENT, `name` VARCHAR(50) NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`) ) COLLATE='utf8_unicode_ci';";
                  
         db.execSQL(createQuery); // execute the query
      } // end method onCreate

      @Override
      public void onUpgrade(SQLiteDatabase db, int oldVersion, 
          int newVersion) 
      {
    	  String updateQuery = "drop table `system`";
      
    	  db.execSQL(updateQuery); // execute the query
      } // end method onUpgrade
   } // end class DatabaseOpenHelper
} // end class DatabaseConnector