<?php
			function printChar2Br($char) {
				$tSize = strlen($char);
				for ($i=0; $i<$tSize; $i++) {
					echo $char[$i] . '<br>';
				}
			}
			$oneDay = 86400;
			// 오늘(D)
			$sql = " select vs_count as cnt from ".$g5['visit_sum_table']."
						where vs_date between '".date("Y-m-d",time())."' and '".date("Y-m-d",time())."' ";
			$row = sql_fetch($sql);
			$visit_cnt[0] = $row['cnt'];
			$visit_href_today = G5_ADMIN_URL."/visit_list.php?fr_date=".date("Y-m-d",time())."&to_date=".date("Y-m-d",time());

			// D-1
			$sql = " select vs_count as cnt from ".$g5['visit_sum_table']."
						where vs_date between '".date("Y-m-d",time()-$oneDay)."' and '".date("Y-m-d",time()-$oneDay)."' ";
			$row = sql_fetch($sql);
			$visit_cnt[1] = $row['cnt'];
			$visit_href_l1 = G5_ADMIN_URL."/visit_list.php?fr_date=".date("Y-m-d",time()-$oneDay)."&to_date=".date("Y-m-d",time()-$oneDay);

			// D-2
			$sql = " select vs_count as cnt from ".$g5['visit_sum_table']."
						where vs_date between '".date("Y-m-d",time()-$oneDay*2)."' and '".date("Y-m-d",time()-$oneDay*2)."' ";
			$row = sql_fetch($sql);
			$visit_cnt[2] = $row['cnt'];
			$visit_href_l2 = G5_ADMIN_URL."/visit_list.php?fr_date=".date("Y-m-d",time()-$oneDay*2)."&to_date=".date("Y-m-d",time()-$oneDay*2);

			// D-3
			$sql = " select vs_count as cnt from ".$g5['visit_sum_table']."
						where vs_date between '".date("Y-m-d",time()-$oneDay*3)."' and '".date("Y-m-d",time()-$oneDay*3)."' ";
			$row = sql_fetch($sql);
			$visit_cnt[3] = $row['cnt'];
			$visit_href_l3 = G5_ADMIN_URL."/visit_list.php?fr_date=".date("Y-m-d",time()-$oneDay*3)."&to_date=".date("Y-m-d",time()-$oneDay*3);

			// D-4
			$sql = " select vs_count as cnt from ".$g5['visit_sum_table']."
						where vs_date between '".date("Y-m-d",time()-$oneDay*4)."' and '".date("Y-m-d",time()-$oneDay*4)."' ";
			$row = sql_fetch($sql);
			$visit_cnt[4] = $row['cnt'];
			$visit_href_l4 = G5_ADMIN_URL."/visit_list.php?fr_date=".date("Y-m-d",time()-$oneDay*4)."&to_date=".date("Y-m-d",time()-$oneDay*4);

			// D-5
			$sql = " select vs_count as cnt from ".$g5['visit_sum_table']."
						where vs_date between '".date("Y-m-d",time()-$oneDay*5)."' and '".date("Y-m-d",time()-$oneDay*5)."' ";
			$row = sql_fetch($sql);
			$visit_cnt[5] = $row['cnt'];
			$visit_href_l5 = G5_ADMIN_URL."/visit_list.php?fr_date=".date("Y-m-d",time()-$oneDay*5)."&to_date=".date("Y-m-d",time()-$oneDay*5);

			// D-6
			$sql = " select vs_count as cnt from ".$g5['visit_sum_table']."
						where vs_date between '".date("Y-m-d",time()-$oneDay*6)."' and '".date("Y-m-d",time()-$oneDay*6)."' ";
			$row = sql_fetch($sql);
			$visit_cnt[6] = $row['cnt'];
			$visit_href_l6 = G5_ADMIN_URL."/visit_list.php?fr_date=".date("Y-m-d",time()-$oneDay*6)."&to_date=".date("Y-m-d",time()-$oneDay*6);

			// 금월
			$sql = " select sum(vs_count) as cnt from ".$g5['visit_sum_table']."
						where vs_date between '".date("Y-m-01",time())."' and '".date("Y-m-d",time())."' ";
			$row = sql_fetch($sql);
			$visit_cnt[month] = $row['cnt'];
			$visit_href_thismonth = G5_ADMIN_URL."/visit_date.php?fr_date=".date("Y-m-01",time())."&to_date=".date("Y-m-d",time());

			// 금년
			$sql = " select sum(vs_count) as cnt from ".$g5['visit_sum_table']."
						where vs_date between '".date("Y-01-01",time())."' and '".date("Y-m-d",time())."' ";
			$row = sql_fetch($sql);
			$visit_cnt[year] = $row['cnt'];
			$visit_href_thisyear = G5_ADMIN_URL."/visit_month.php?fr_date=".date("Y-01-01",time())."&to_date=".date("Y-m-d",time());

			// 전체
			$sql = " select sum(vs_count) as cnt from ".$g5['visit_sum_table']." ";
			$row = sql_fetch($sql);
			$visit_cnt[total] = $row['cnt'];
			$visit_href_total = G5_ADMIN_URL."/visit_month.php?fr_date=".date("2000-01-01",time())."&to_date=".date("Y-m-d",time());

			$visit_cnt['max'] = max($visit_cnt[0], $visit_cnt[1], $visit_cnt[2], $visit_cnt[3], $visit_cnt[4], $visit_cnt[5], $visit_cnt[6]);
			$visit_cnt['sum'] = $visit_cnt[0] + $visit_cnt[1] + $visit_cnt[2] + $visit_cnt[3] + $visit_cnt[4] + $visit_cnt[5] + $visit_cnt[6];
			for ($i=0; $i<=6; $i++) {
				$visit_cnt_percent[$i] = round(($visit_cnt[$i] / $visit_cnt['sum'] * 100), 1);
				$visit_cnt_height[$i] = round((($visit_cnt[$i] / $visit_cnt['max'] * 100) * 0.85), 0);
			}
			$sql = " select max(vs_count) as cnt from ".$g5['visit_sum_table']." ";
			$row = sql_fetch($sql);
			$vi_max = $row[cnt];
?>
		  <TABLE cellSpacing=0 cellPadding=0 border=0>
			  <TR height="20" valign="middle" style="PADDING-LEFT: 0px;">
			    <TD colspan=7>최근방문자현황</TD>
			  </TR>
			<TR>
				<TD bgColor=#2c8ee5 colSpan=7 height=2></TD></TR>
			<TR>
			<TR align=middle>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35" height=25><?php echo $visit_cnt[6];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $visit_cnt[5];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $visit_cnt[4];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $visit_cnt[3];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $visit_cnt[2];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $visit_cnt[1];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $visit_cnt[0];?></TD></TR>
			<TR vAlign=bottom align=middle>
				<TD height=110><IMG height=<?php echo $visit_cnt_height[6];?> src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height=<?php echo $visit_cnt_height[5];?> src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height=<?php echo $visit_cnt_height[4];?> src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height=<?php echo $visit_cnt_height[3];?> src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height=<?php echo $visit_cnt_height[2];?> src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height=<?php echo $visit_cnt_height[1];?> src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height=<?php echo $visit_cnt_height[0];?> src="<?php echo $DracoCounter_URL; ?>/img/graph_status_t.gif" width=10></TD></TR>
			<TR bgColor=#cccccc>
				<TD colSpan=7 height=1></TD></TR>
			<TR>
				<TD colSpan=7 height=5></TD></TR>
			<TR align=middle>
				<TD height=40><A HREF="<?php echo $visit_href_l6;?>"><SPAN <?php echo $mouseEventwebcText;?>>D-6<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*6));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $visit_href_l5;?>"><SPAN <?php echo $mouseEventwebcText;?>>D-5<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*5));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $visit_href_l4;?>"><SPAN >D-4<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*4));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $visit_href_l3;?>"><SPAN <?php echo $mouseEventwebcText;?>>D-3<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*3));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $visit_href_l2;?>"><SPAN <?php echo $mouseEventwebcText;?>>D-2<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*2));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $visit_href_l1;?>"><SPAN <?php echo $mouseEventwebcText;?>>D-1<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*1));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $visit_href_today;?>"><SPAN style="LETTER-SPACING: -0.1EM;" <?php echo $mouseEventwebcText;?> >오늘<br>(<?php echo get_yoil(date("Y-m-d",time()));?>)</SPAN></A></TD>
			</TR>

			<TR align=middle>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;" height=25><?php echo $visit_cnt_percent[6];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $visit_cnt_percent[5];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $visit_cnt_percent[4];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $visit_cnt_percent[3];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $visit_cnt_percent[2];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $visit_cnt_percent[1];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $visit_cnt_percent[0];?>%</TD></TR>
		  </TABLE>