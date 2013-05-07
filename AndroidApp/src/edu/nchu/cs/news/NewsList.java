package edu.nchu.cs.news;

import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.Button;

public class NewsList extends Activity {

	private static final String ACTIVITY_TAG="NewsList";
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_news_list);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.news_list, menu);
		return true;
	}
	
	private View.OnClickListener clickViewNews = new View.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			try{
				int nid = Integer.parseInt( (String) v.getTag() );
				Intent intent = new Intent();
				intent.setClass(NewsList.this, ViewNews.class);
				Bundle bundle = new Bundle();
				bundle.putInt("NID", nid);
				intent.putExtras(bundle);
				startActivity(intent);
			}
			catch(Exception e)
			{
				Log.e(ACTIVITY_TAG, e.toString());
			}
		}
	};
}
