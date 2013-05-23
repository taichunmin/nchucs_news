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
import android.view.View.OnClickListener;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;

public class FilterActivity extends Activity {

	private TextView tv_filterTitle;
	private final String ACTIVITY_TAG = "Filter";
	boolean filterRid = false;
	private LayoutInflater inflater;
	private String filter;
	private NewsDataModel newsDataModel;
	private LinearLayout ll_filterListContent;
	private ArrayList<HashMap<String, String>> filterItems;
	private HashMap<String, String> rssNameMap = null;
	protected static final int handle_addFilterList = 0x10001;
	private ProgressBar circleProgressBar;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_filter);
		findViews();
		setListeners();
		processIntent();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.filter, menu);
		return true;
	}

	private void findViews() {
		tv_filterTitle = (TextView) findViewById(R.id.tv_filterTitle);
		ll_filterListContent = (LinearLayout) findViewById(R.id.ll_filterListContent);
		inflater = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		circleProgressBar = (ProgressBar) findViewById(R.id.circleProgressBar);
		newsDataModel = new NewsDataModel(this);
	}

	private void setListeners() {
	}

	private void processIntent() {
		try {
			Bundle bundle = this.getIntent().getExtras();
			if (bundle == null)
				finish(); // end activity
			filter = bundle.getString("FILTER");
			if (filter.equals("rid"))
				filterRid = true;
		} catch (Exception e) {
			if (e.getMessage().length() != 0)
				Log.e(ACTIVITY_TAG, e.getMessage());
			filterRid = false;
		}
		if (filterRid)
			tv_filterTitle.setText(getText(R.string.filter_rid));
		else
			tv_filterTitle.setText(getText(R.string.filter_date));
		addFilterListGUI();
	}

	private void addFilterListGUI() {
		showProgressBar();
		Thread mThread = new Thread(new Runnable() {

			public void run() {
				try {
					if(rssNameMap == null)
						rssNameMap = newsDataModel.fetch_rss_name();
					if (filterRid) {
						filterItems = newsDataModel.cnt_cate();
					} else {
						filterItems = newsDataModel.cnt_day();
					}
					Message msg = new Message();
					msg.what = handle_addFilterList;
					mHandler.sendMessage(msg);
				} catch (Exception e) {
					Log.e(ACTIVITY_TAG, e.getMessage());
				}
			}
		});
		mThread.start();
	}

	private void showProgressBar() {
		circleProgressBar.setVisibility(View.VISIBLE);
		circleProgressBar.setProgress(0);
	}

	private void hideProgressBar() {
		circleProgressBar.setVisibility(View.GONE);
	}

	private void addFilterListGUIHandle() {
		if (filterItems == null) {
			Log.e(ACTIVITY_TAG, "addFilterListGUIHandle null error.");
		}
		try {
			for (int i = 0; i < filterItems.size(); i++) {
				HashMap<String, String> item = (HashMap<String, String>) filterItems
						.get(i);
				View ListItemView = inflater.inflate(
						R.layout.new_filter_list_view, ll_filterListContent,
						false);

				TextView tv_filterCnt = (TextView) ListItemView
						.findViewById(R.id.tv_filterCnt), tv_filterText = (TextView) ListItemView
						.findViewById(R.id.tv_filterText);

				ListItemView.setOnClickListener(clickNewsList);
				ListItemView.setTag(item.get(filterRid ? "rid" : "date"));
				tv_filterText.setText(filterRid ? rssNameMap.get(item.get("rid")) : item.get("date"));
				tv_filterCnt.setText(item.get("cnt"));

				ll_filterListContent.addView(ListItemView);
			}
		} catch (Exception e) {
			Log.e(ACTIVITY_TAG, e.getMessage());
		}
		filterItems = null;
		hideProgressBar();
	}

	@SuppressLint("HandlerLeak")
	private Handler mHandler = new Handler() {
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case handle_addFilterList:
				addFilterListGUIHandle();
				break;
			}
		}
	};

	private View.OnClickListener clickNewsList = new View.OnClickListener() {
		@Override
		public void onClick(View v) {
			try {
				Intent intent = new Intent();
				intent.setClass(FilterActivity.this, NewsList.class);
				Bundle bundle = new Bundle();
				bundle.putString("DATA", (String) v.getTag());
				bundle.putString("TITLE", filterRid ? rssNameMap.get((String) v.getTag()) : (String) v.getTag());
				bundle.putString("LIST_TYPE", filter);
				intent.putExtras(bundle);
				startActivity(intent);
			} catch (Exception e) {
				Log.e(ACTIVITY_TAG, e.toString());
			}
		}
	};
}
