package edu.nchu.cs.news;

import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.view.Menu;
import android.view.View;
import android.widget.Button;

public class MainActivity extends Activity {
	
	Button btn_view_news, btn_login, btn_news_list, btn_filter;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		findViews();
		setListeners();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}
	
	private void findViews()
	{
		btn_view_news = (Button) findViewById( R.id.btn_view_news );
		btn_login = (Button) findViewById( R.id.btn_login );
		btn_news_list = (Button) findViewById( R.id.btn_news_list );
		btn_filter = (Button) findViewById( R.id.btn_filter );
	}
	
	private void setListeners()
	{
		btn_view_news.setOnClickListener(listen_view_news);
		btn_login.setOnClickListener(listen_login);
		btn_news_list.setOnClickListener(listen_news_list);
		btn_filter.setOnClickListener(listen_filter);
	}

	private Button.OnClickListener listen_view_news = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			startActivity( new Intent().setClass(MainActivity.this, ViewNews.class) );
		}
	};

	private Button.OnClickListener listen_login = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			startActivity( new Intent().setClass(MainActivity.this, LoginActivity.class) );
		}
	};

	private Button.OnClickListener listen_news_list = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			startActivity( new Intent().setClass(MainActivity.this, NewsList.class) );
		}
	};

	private Button.OnClickListener listen_filter = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			startActivity( new Intent().setClass(MainActivity.this, FilterActivity.class) );
		}
	};
	
}
