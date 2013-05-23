package edu.nchu.cs.news;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;

import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.os.Build;
import android.util.Log;
import edu.nchu.cs.news.NewsDbHelper;

public class NewsDbConnector {

	private SQLiteDatabase mNewsDbRW;
	private NewsDbHelper newsDbHp; // database helper

	private static final String DB_NAME = "nchucsnews.db",
			DB_SYS_TABLE = "system", DB_NEWS_TABLE = "news_cache",
			ACTIVITY_TAG = "NewsDbConnector";
	private final int _DBVersion = 3;

	public NewsDbConnector(Context context) {
		newsDbHp = new NewsDbHelper(context, DB_NAME, null, _DBVersion);
		initial();
	}
	
	private void dbOpen()
	{
		mNewsDbRW = newsDbHp.getWritableDatabase();
	}
	
	private void dbClose()
	{
		mNewsDbRW.close();
	}

	private void initial() {
		String sql = "insert or ignore into " + DB_SYS_TABLE
				+ " (`index`,`value`) values ";
		String[] sysName = { "token", "limit_per_page", "cache_exist_time",
				"simi_1st", "simi_2st", "simi_3st", "onto_limit" }, sysValue = {
				"", "200", "3", "60", "30", "10", "100" };
		dbOpen();
		for (int i = 0; i < sysName.length && i < sysValue.length; i++)
			mNewsDbRW.execSQL(sql + "('" + sysName[i] + "','" + sysValue[i]
					+ "');");
		dbClose();
	}

	public void systemPut(String index, String value) throws SQLException {

		dbOpen();
		mNewsDbRW.execSQL("insert into `" + DB_SYS_TABLE
				+ "` (`index`,`value`) values (?,?)", new String[] { index,
				value });
		dbClose();

	}

	public void systemSet(String index, String value) throws SQLException {

		dbOpen();
		mNewsDbRW.execSQL("update `" + DB_SYS_TABLE
				+ "` set `value`=? where `index`=? ", new String[] { value,
				index });
		dbClose();
	}

	public void newsPut(HashMap<String, String> map) throws SQLException,
			Exception {

		String[] array_keys = { "_id", "title", "content", "date", "url" };

		for (int i = 0; i < array_keys.length; i++)
			if (!map.containsKey(array_keys[i]))
				throw new Exception("keyInvaild");

		dbOpen();
		mNewsDbRW
				.execSQL(
						"insert into `"
								+ DB_NEWS_TABLE
								+ "` (`_id`,`title`,`content`,`date`,`url`) values (?,?,?,?,?) ",
						new String[] { map.get("_id"), map.get("title"),
								map.get("content"), map.get("date"),
								map.get("url") });
		dbClose();

	}

	public void newsSet(String _id, String title, String content, String date,
			String url) {

		try {
			ContentValues newRow = new ContentValues();

			newRow.put("title", title);
			newRow.put("content", content);
			newRow.put("date", date);
			newRow.put("url", url);

			dbOpen();
			mNewsDbRW.update(DB_NEWS_TABLE, newRow, "`_id`=?",
					new String[] { _id });
			dbClose();
		} catch (Exception e) {
			int lineNum = Thread.currentThread().getStackTrace()[2]
					.getLineNumber();
			Log.e(ACTIVITY_TAG, lineNum + ": " + e.toString());
		}
	}

	public void systemDelByIndex(String index) {
		dbOpen();
		mNewsDbRW.delete(DB_SYS_TABLE, "`index`=?", new String[] { index });
		dbClose();
	}

	public void newsDelById(String _id) {
		dbOpen();
		mNewsDbRW.delete(DB_NEWS_TABLE, "`_id`=?", new String[] { _id });
		dbClose();
	}

	public HashMap<String, String> systemGetByIndex(String index)
			throws Exception {

		HashMap<String, String> map = new HashMap<String, String>();
		dbOpen();
		Cursor cursor = mNewsDbRW.rawQuery("select `index`, `value` from "
				+ DB_SYS_TABLE + " where `index`=?", new String[] { index });
		if (cursor == null)
			throw new Exception("sqlError");
		if (cursor.getCount() == 0)
			throw new Exception("sqlNoData");
		cursor.moveToFirst();
		map.put("index", cursor.getString(0));
		map.put("value", cursor.getString(1));
		cursor.close();
		dbClose();
		return map;
	}

	public HashMap<String, String> newsGetById(int nid) throws Exception {

		String _id = "" + nid;
		HashMap<String, String> map = new HashMap<String, String>();
		Cursor cursor = null ;
		try{
			dbOpen();
			cursor = mNewsDbRW.rawQuery(
					"select `_id`, `title`, `content`, `url`, `date`, `cache_time` from "
							+ DB_NEWS_TABLE + " where `_id`=?",
					new String[] { _id });
			// Need to be fix (handle no nid)
			if (cursor == null)
				throw new Exception("sqlError");
			if (cursor.getCount() == 0)
				throw new Exception("sqlNoData");
			cursor.moveToFirst();
			map.put("_id", cursor.getInt(0) + "");
			map.put("title", cursor.getString(1));
			map.put("content", cursor.getString(2));
			map.put("url", cursor.getString(3));
			map.put("date", cursor.getString(4));
			map.put("cache_time", cursor.getString(5));
			cursor.close();
			dbClose();
		}catch(Exception e)
		{
			cursor.close();
			throw e;
		}
		return map;
	}

	@SuppressLint("SimpleDateFormat")
	public ArrayList<String> getOverTimeNewsId(int days) {
		Calendar now = Calendar.getInstance();
		now.add(Calendar.DATE, -days);
		SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
		String date = dateFormat.format(now.getTime());
		ArrayList<String> list = new ArrayList<String>();
		dbOpen();
		Cursor cursor = mNewsDbRW.rawQuery("select `_id` from " + DB_NEWS_TABLE
				+ " where `cache_time`<=?", new String[] { date });
		if (cursor.moveToFirst()) {
			do {
				list.add(cursor.getString(0));
			} while (cursor.moveToNext());
		}
		cursor.close();
		dbClose();
		return list;
	}

	@TargetApi(Build.VERSION_CODES.HONEYCOMB)
	private HashMap<String, String> cursor2HashMap(Cursor c) {
		HashMap<String, String> map = new HashMap<String, String>();
		for (int i = 0; i < c.getColumnCount(); i++) {
			switch (c.getType(i)) {
			case Cursor.FIELD_TYPE_NULL:
				map.put(c.getColumnName(i), "");
				break;
			case Cursor.FIELD_TYPE_INTEGER:
				map.put(c.getColumnName(i), c.getInt(i) + "");
				break;
			case Cursor.FIELD_TYPE_FLOAT:
				map.put(c.getColumnName(i), c.getFloat(i) + "");
				break;
			case Cursor.FIELD_TYPE_STRING:
				map.put(c.getColumnName(i), c.getString(i));
				break;
			case Cursor.FIELD_TYPE_BLOB:
				map.put(c.getColumnName(i), c.getBlob(i) + "");
				break;
			}
		}
		return map;
	}
}
