import java.sql.*;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

public class DeleteComment{
    public static void main(String[] args){
        deleteDataBase();
    }
    public static void deleteDataBase(){
        //新しい10記事だけ残して、あとはデータベースから削除
        Connection conn = null;
        try {
        conn = DriverManager.getConnection("jdbc:mysql://localhost/fx", "root", "**password**");
    
        Statement stmt = conn.createStatement();
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date date = new Date();
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(date);
        calendar.add(Calendar.DAY_OF_MONTH, -7);
        Date oneweekagoDate = calendar.getTime();
        String oneweekagoStr = sdf.format(oneweekagoDate);
        System.out.println(oneweekagoStr);
        String sql = "delete from comment where date < \"" + oneweekagoStr + "\";";
        stmt.executeUpdate(sql);
        
        }catch (SQLException e){
            System.out.println("SQLException:" + e.getMessage());
        }
    }
}
