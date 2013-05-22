package edu.nchu.cs.news;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteDatabase.CursorFactory;
import android.database.sqlite.SQLiteOpenHelper;

public class NewsDbHelper extends SQLiteOpenHelper {

	public static final String DB_SYS_TABLE = "system",
			DB_NEWS_TABLE = "news_cache";
	private final String DB_NAME = "nchucsnews", ACTIVITY_TAG = "NewsDbHelper";
	private String[] sCreateTableSQL = {
			"CREATE TABLE `" + DB_SYS_TABLE + "` ("
					+ "`index` VARCHAR(128) NOT NULL ,"
					+ "`value` TEXT NOT NULL ,"
					+ "PRIMARY KEY (`index`)" + ")", 
			"CREATE TABLE `news_cache` ("
					+ "`_id` integer PRIMARY KEY AUTOINCREMENT,"
					+ "`title` TEXT NOT NULL DEFAULT '' ,"
					+ "`date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',"
					+ "`content` TEXT NOT NULL DEFAULT '' ,"
					+ "`url` TEXT NOT NULL DEFAULT '' ,"
					+ "`cache_time` TIMESTAMP NOT NULL DEFAULT (datetime('now','localtime')))" },
			dbTableArray = { DB_SYS_TABLE, DB_NEWS_TABLE };

	public NewsDbHelper(Context context, String name, CursorFactory factory,
			int version) {
		super(context, name, factory, version);
	}

	@Override
	public void onCreate(SQLiteDatabase db) {
		for (int i = 0; i < sCreateTableSQL.length; i++) {
			if (sCreateTableSQL[i].length() == 0)
				continue;
			db.execSQL(sCreateTableSQL[i]);
		}
	}

	@Override
	public void onUpgrade(SQLiteDatabase db, int oldVer, int newVer) {
		// TODO Auto-generated method stub
		for (int i = 0; i < dbTableArray.length; i++)
			db.execSQL("DROP TABLE IF EXISTS `" + dbTableArray[i] + "`");
		onCreate(db);
	}

}
