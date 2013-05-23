package edu.nchu.cs.news;

import java.util.ArrayList;
import java.util.HashMap;

import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.TextView;

public class NewsList extends Activity {

	private static final String ACTIVITY_TAG = "NewsList";
	private ProgressBar circleProgressBar;
	private LayoutInflater inflater;
	private LinearLayout ll_newsListContent;
	private TextView tv_newsListTitle;
	private int ListType = 0; // 0=today, 1=date, 2=cateDay
	protected static final int handle_addNewsList = 0x10001;
	private ArrayList<HashMap<String, String>> newsItems = null;
	private NewsDataModel newsDataModel;
	private String filterData = null;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_news_list);
		findViews();
		setListeners();
		processIntent();
	}

	private void processIntent() {
		try {
			Bundle bundle = this.getIntent().getExtras();
			String ListTypeStr = null;
			if (bundle != null)
				ListTypeStr = bundle.getString("LIST_TYPE");
			if (ListTypeStr == null)
				ListTypeStr = "today";
			if (ListTypeStr.equals("date")) {
				ListType = 1;
				tv_newsListTitle.setText(bundle.getString("TITLE"));
				filterData = bundle.getString("DATA");
			} else if (ListTypeStr.equals("rid")) {
				ListType = 2;
				tv_newsListTitle.setText(bundle.getString("TITLE"));
				filterData = bundle.getString("DATA");
			} else
				throw new Exception("");
		} catch (Exception e) {
			if (e.getMessage().length() != 0)
				Log.e(ACTIVITY_TAG, e.getMessage());
			tv_newsListTitle.setText(getText(R.string.list_type_today));
			ListType = 0;
		}
		addNewsListGUI();
	}

	private void findViews() {
		tv_newsListTitle = (TextView) findViewById(R.id.tv_newsListTitle);
		ll_newsListContent = (LinearLayout) findViewById(R.id.ll_newsListContent);
		inflater = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		circleProgressBar = (ProgressBar) findViewById(R.id.circleProgressBar);
		newsDataModel = new NewsDataModel(this);
	}

	private void setListeners() {
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.news_list, menu);
		return true;
	}

	private View.OnClickListener clickViewNews = new View.OnClickListener() {
		@Override
		public void onClick(View v) {
			try {
				Intent intent = new Intent();
				intent.setClass(NewsList.this, ViewNews.class);
				Bundle bundle = new Bundle();
				bundle.putString("NID", (String) v.getTag());
				intent.putExtras(bundle);
				startActivity(intent);
			} catch (Exception e) {
				Log.e(ACTIVITY_TAG, e.toString());
			}
		}
	};

	@SuppressLint("HandlerLeak")
	private Handler mHandler = new Handler() {
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case handle_addNewsList:
				addNewsListGUIHandle();
				break;
			}
		}
	};

	private void addNewsListGUI() {

		showProgressBar();

		Thread mThread = new Thread(new Runnable() {

			public void run() {
				try {
					switch (ListType) {
					case 0: // today
						newsItems = newsDataModel.newslist_today();
						break;
					case 1: // day
						newsItems = newsDataModel.newslist_day(filterData);
						break;
					case 2: // category
						newsItems = newsDataModel.newslist_cate(Integer.parseInt(filterData));
						break;
					}
					Message msg = new Message();
					msg.what = handle_addNewsList;
					mHandler.sendMessage(msg);
				} catch (Exception e) {
					Log.e(ACTIVITY_TAG, e.getMessage());
				}
			}
		});
		mThread.start();
	}

	private void addNewsListGUIHandle() {
		if (newsItems == null) {
			Log.e(ACTIVITY_TAG, "addNewsListGUIHandle null error.");
		}
		try {
			for (int i = 0; i < newsItems.size(); i++) {
				HashMap<String, String> item = (HashMap<String, String>) newsItems
						.get(i);
				View ListItemView = inflater.inflate(
						R.layout.new_news_list_view, ll_newsListContent, false);

				TextView tv_newsItemDate = (TextView) ListItemView
						.findViewById(R.id.tv_newsItemDate), tv_newsItemTitle = (TextView) ListItemView
						.findViewById(R.id.tv_newsItemTitle);

				ListItemView.setOnClickListener(clickViewNews);
				ListItemView.setTag(item.get("nid"));
				tv_newsItemTitle.setText(item.get("title"));
				tv_newsItemDate.setText(item.get("date"));

				ll_newsListContent.addView(ListItemView);
			}
		} catch (Exception e) {
			Log.e(ACTIVITY_TAG,e.getMessage());
		}
		newsItems = null;
		hideProgressBar();
	}

	private void showProgressBar() {
		circleProgressBar.setVisibility(View.VISIBLE);
		circleProgressBar.setProgress(0);
	}

	private void hideProgressBar() {
		circleProgressBar.setVisibility(View.GONE);
	}
}
