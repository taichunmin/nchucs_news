package edu.nchu.cs.news;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Map;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.util.Log;
import edu.nchu.cs.news.NewsDbHelper;



public class NewsDbConnector {
	
	private SQLiteDatabase mNewsDbRW;
	private NewsDbHelper newsDbHp; // database helper
	
	private static final String DB_NAME = "nchucsnews",
								DB_SYS_TABLE = "system",
								DB_NEWS_TABLE = "news_cache",
								ACTIVITY_TAG = "Database";
	
	public NewsDbConnector(Context context){
		newsDbHp =  new NewsDbHelper(context, DB_NAME, null, 1);

		newsDbHp.sCreateTableCommand = "CREATE TABLE " + DB_SYS_TABLE + "(" +
				"sys_id INTEGER PRIMARY NOT NULL KEY AUTO_INCREMENT," +
				"index VARCHAR(50) NOT NULL," +
				"value VARCHAR(50) NOT NULL," +
				"COLLATE='utf8_unicode_ci';";

		mNewsDbRW = newsDbHp.getWritableDatabase();
		mNewsDbRW.close();
		
		newsDbHp.sCreateTableCommand = "CREATE TABLE " + DB_NEWS_TABLE + "(" +
				"news_id INTEGER PRIMARY NOT NULL KEY AUTO_INCREMENT," +
				"title VARCHAR(50) NOT NULL," +
				"content TEXT NOT NULL," +
				"createdate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP )" +
				"COLLATE='utf8_unicode_ci';";

		mNewsDbRW = newsDbHp.getWritableDatabase();		
		mNewsDbRW.close();

		initial();
	}

	private void initial() {
		systemPut("token", "0");
		systemPut("limit_per_page", "200");
		systemPut("cache_exist_time", "3");
		systemPut("simi_1st", "60");
		systemPut("simi_1st", "30");
		systemPut("simi_1st", "10");
		systemPut("onto_limit", "100");
	}

	public void dbOpen(String type) throws SQLException {

		try {
			if (type == "r")
				mNewsDbRW = newsDbHp.getReadableDatabase();
			else if (type == "w")
				mNewsDbRW = newsDbHp.getWritableDatabase();
			else {}
		}
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }

	}
	
	public void dbClose() {

		try {
			if (mNewsDbRW != null)
				mNewsDbRW.close(); // close the database connection
		}
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }
	}	
	
	public void systemPut(String index, String value){
		
		try{
			ContentValues newRow = new ContentValues();
		
			newRow.put("index", index);
	        newRow.put("value", value);
	        
	        dbOpen("r");
	        mNewsDbRW.insertOrThrow(DB_SYS_TABLE, null, newRow);
	        dbClose();

        }
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }
	}

	public void newsPut(Map<String, String> map){
		
		try {
			ContentValues newRow = new ContentValues();
			
			newRow.put("_id", map.get("_id"));
	        newRow.put("title", map.get("title"));
	        newRow.put("content", map.get("content"));
	        newRow.put("date", map.get("date"));
	        newRow.put("url", map.get("url"));
	       
	        dbOpen("r");
	        mNewsDbRW.insertOrThrow(DB_NEWS_TABLE, null, newRow);
	        dbClose();
		}
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }	        
	}	
	
	public void systemSet(String index, String value){

		try {
			ContentValues newRow = new ContentValues();
			
	        newRow.put("value", value);
	        
	        dbOpen("r");
	        mNewsDbRW.update(DB_SYS_TABLE, newRow, "index=?", new String[] { index });
	        dbClose();
		}
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }
	}

	public void newsSet(String _id, String title, String content, String date, String url){

		try {
			ContentValues newRow = new ContentValues();
			
			newRow.put("title", title);
		    newRow.put("content", content);
			newRow.put("date", date);
			newRow.put("url", url);
		    
		    dbOpen("r");
		    mNewsDbRW.update(DB_NEWS_TABLE, newRow, "_id=?", new String[] { _id });
		    dbClose();
		}
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }		    
	}
	
	public void systemDelByIndex(String index){

		try {		
	        dbOpen("r");
	        mNewsDbRW.delete(DB_SYS_TABLE, "index=?", new String[] { index });
	        dbClose();
	    }
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }
	}

	public void newsDelById(String _id){

		try {
	        dbOpen("r");
	        mNewsDbRW.delete(DB_NEWS_TABLE, "_id=?", new String[] { _id });
	        dbClose();
	    }
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }
	}		
	
	public String systemGetByIndex(String index){

		Map<String, String> map = new HashMap<String, String>();
		try {
			

			dbOpen("r");
	        Cursor cursor = mNewsDbRW.rawQuery(
	                "select _id, index, value from " + DB_SYS_TABLE + "where index=?",
	                new String[]{index});
		 	while (cursor.moveToNext()) {
			 	map.put("id",      	cursor.getString(0));
	            map.put("index",	cursor.getString(1));
	            map.put("value",    cursor.getString(2));
			}
			cursor.close();
			dbClose();

			
		}
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }
		return map.get("value");
	}

	public Map<String, String> newsGetById(int nid){

		String _id = ""+nid;
		Map<String, String> map = new HashMap<String, String>();
		try {

	        dbOpen("r");
	        Cursor cursor = mNewsDbRW.rawQuery(
	                "select _id, title, content, url, date, cache_time from " + DB_NEWS_TABLE + "where _id=?",
	                new String[]{_id});
			while (cursor.moveToNext()) {
				map.put("_id",      	cursor.getString(0));
				map.put("title",		cursor.getString(1));
				map.put("content",		cursor.getString(2));
				map.put("url",    		cursor.getString(3));
				map.put("date",			cursor.getString(4));
				map.put("cache_time",	cursor.getString(5));
			}
			cursor.close();
			dbClose();

		}
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }
		return map;
	}	

    public ArrayList<String> getOverTimeNewsId(int days) {
        Calendar now = Calendar.getInstance();
        now.add(Calendar.DATE,-days);
        SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
		String date = dateFormat.format(now.getTime());
        ArrayList<String> list = new ArrayList<String>();

		try {

	        dbOpen("r");
	        Cursor cursor = mNewsDbRW.rawQuery(
	                "select _id from " + DB_NEWS_TABLE + "where cache_time<=?",
	                new String[]{date});
			while (cursor.moveToNext()) {
				list.add(cursor.getString(0));
			}
			cursor.close();
			dbClose();

		}
        catch( Exception e ) {
            Log.e(ACTIVITY_TAG,e.toString());
        }
		return list;
    }
}