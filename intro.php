<!DOCTYPE html>
<html>
	<head>
		<title>新聞推薦系統行動版</title> 
		<meta charset="utf-8" />
		<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width" />
		
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
		<script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
		<style>
			p{
				line-height: 150%;
			}
		</style>
	</head>
	<body>
	<div data-role="page" id="" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1>新聞推薦系統</h1>
		</div>
		<div data-role="content">
				<table><tr>
				<th style="width:240px">
					<img src="images/news.png" style="width:70%; max-width:240px;" />
					<div style=" color: #aaaaaa; margin-top: 5px; font-size:x-small; ">&copy; 2012 DMLAB.CS.NCHU.EDU.TW</div>
				</th>
				<td>
						<p>網路普及與資訊服務的蓬勃發展，使得網路上的資料量越來越大，如何有效的擷取出有意義的資訊是一項大課題，所以搜尋或推薦系統在目前網路服務研究上受到重視。
			為了讓讀者可以快速地獲得自己有興趣的新聞，本專題研究主要探討針對讀者找出其閱報喜好進而快速尋找近期可能喜好的新聞文章，提出一個基於個人本體論的新聞推薦模式(RO-NRS, News Recommender System based on Personal Ontology)。
			本專題研究以網站方式展示本專題研究成果，為了讓讀者可以方便使用本系統，也導入了新聞行動推薦服務。</p>
						<p>
			台灣熟悉的網路新聞有二， Yahoo新聞與Google新聞；Yahoo新聞的推薦方式是利用使用者點選看完新聞後的感受，比如：實用、感人、開心等等，並在首頁列出屬於該心情分類的新聞列表，使用者可以藉此為依據看到想看的新聞。Google新聞的推薦方式，是採用使用者自定版面與其搜索關鍵字，對新聞標題與內容進行搜尋，任何有相關字詞的新聞都會歸納到版面中，呈現使用者想看的新聞。
		但是，我們認為使用者會專注在某個事件或是新聞分類之上，而非滿足於心情排行；心情排行的依據基於使用者的回饋，沒有一個明確的定義來分類新聞，這種不具個人特色的分類方式不夠可靠。此外，我們也認為大部分的使用者，不願意建立專屬的自訂版面。
		所以，我們理想中的系統，必須有一個明確定義分類的方式，藉此建立使用者新聞喜好的推薦規則，並且一切都要由系統自動完成。</p>
					<div style="clear: both">&nbsp;</div>
				</td></tr>
				</table>
		<div>
		<div data-role="footer" data-position="fixed">
			<h4><a href="mailto:taichunmin@gmail.com?subject=nchucs_news">新聞推薦組-組員：戴均民、郭書佑、朱奕安</a></h4>
		</div><!-- /footer -->
	</div>
	</body>
</html>
