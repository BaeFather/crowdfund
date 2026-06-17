import java.io.*;
import java.net.*;
import java.sql.*;
import java.util.*;

import org.apache.log4j.*;


public class EnDeServerThread extends Thread {

	private Logger logger;	//로그처리용
	private Socket recSocket = null;
	private InputStream input = null;
	private OutputStream output;
	private String enc;
	private String key;				//암호화 key.
	private String enData;			//암호화 string.
	private String deData;			//복호화 string.
	private BufferedInputStream bis = null;
	private int MAX_SIZE	=	4096;
	private Socket socket;				
	private String message;				//서버로 전송할 메세지
	private String host;				//Decrypt 처리된 데이터
	private int port_va;				//va서버 port
	private int port_fm;				//fm서버 port
	private int port2;				//클라이언트 port
	private int Ileng = 0;

	/**
	 * 서버처리 스레드
	 * @param socket 서버에서 넘겨받은 소켓객체
	 * @param key 	서버에서 넘겨받은 키값
	 */
	public EnDeServerThread(Socket rec_socket, String key, String host,int port_va,int port_fm, int port2, String enc) {
		this.key = key;
		this.recSocket = rec_socket;
		this.host = host;
		this.port_va = port_va;
		this.port_fm = port_fm;
		this.port2 = port2;
		this.enc = enc;
		
		try {
			logger = Logger.getLogger(EnDeServerThread.class);

			input = recSocket.getInputStream();
			output = recSocket.getOutputStream();
	 

			bis		=	new BufferedInputStream(input);
		
		
		} catch (IOException ioe) {
			logger.info("error_io (EnDeServerThread.java)");
		}
	}

	/**
	 * 스레드 실행
	 */
	public void run() {

		
		if (input != null && output != null) {
			FCreceive();			//client로부터 비암호화값 receive

			if(message != null){

				
				TSsend(message);		//server로 암호화하여 send
				
				FSreceive();			//server에서 암호화한 응답 receive
				
				TCsend(deData);			//client로 암호화 풀어서 send
				
				
			}else
				logger.info("decoding data null...");

		} else
			logger.info("error_input_output (EnDeServerThread.java)");
	}

	
	public void FCreceive() {  	
	
		try {
			int bytesRead =0;
			int sumByte = 0;
			String str = null;
			Ileng = 0;
			byte[] bRecv = new byte[4];
			byte[] bRecv2 = null;
			bis.read(bRecv);
			Ileng = Integer.parseInt(new String(bRecv));
			bRecv2 = new byte[Ileng];
			do{
				 	
				 bytesRead  = bis.read(bRecv2,sumByte, Ileng-sumByte);
				 sumByte = bytesRead + sumByte;
				 str = new String(bRecv2);
				
			}while(sumByte<Ileng);
			bytesRead=0;
			sumByte = 0;
		
			str = ByteSubStr( str, 0, Ileng );
			
			setMessage( str);
			//logger.info("leng = " + str.getBytes().length);
			logger.info("FromCli = " + message);
		
		} catch (IOException ioe) {
			logger.info("receive error = " + ioe.getMessage());
		}	
			
	}
	
	public void FSreceive() {     
	
		try {
			input = socket.getInputStream();
			bis		=	new BufferedInputStream(input);
			
			
			int bytesRead =0;
			int sumByte = 0;
			String str = null;
			
			byte[] bRecv = new byte[4];
			byte[] bRecv2 = null;
			int mlen = 0;
			bis.read(bRecv);
			mlen = Integer.parseInt(new String(bRecv));
			bRecv2 = new byte[mlen];
			do{
				 	
				 bytesRead  = bis.read(bRecv2,sumByte, mlen-sumByte);
				 sumByte = bytesRead + sumByte;
				 str = new String(bRecv2);

			}while(sumByte<mlen);
			bytesRead=0;
			sumByte = 0;
	
			str	 =	str.substring(0, mlen);			//암호화된 전문 
			
			String logStr = mlen + str;
			byte buffer[]	=	str.getBytes();

			if(enc.equals("1")){
				setEnData(new String(buffer));
				byte deDataByte[] = seedDecrypt(base64Decoding(buffer), key);		//Decoding..
				setDeData(new String(deDataByte));
			}else{
				deData = str;
			}
			
			//logger.info("leng = " + logStr.getBytes().length);
			logger.info("FromServer = " + logStr);
		
		} catch (IOException ioe) {
			logger.info("receive error = " + ioe.getMessage());
		}	
			
	}

	

	/**
	 * 클라이언트에게 데이터 전송
	 * @param sendData 전송데이터
	 */
	
	public boolean TSsend(String sendData) {
    int port = 0;
		try
		{
			
			if(Ileng == 200)
				port = port_va;
			else if(Ileng == 300)
				port = port_fm;
			else 
				throw new IOException("port error");
				
			socket = new Socket(host, port);
			input = socket.getInputStream();
			output = socket.getOutputStream();
				
			BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(output));

			if(enc.equals("1")){
				byte enDataByte[] =  base64Encoding(seedEncrypt(sendData, key));
				setEnData(new String(enDataByte));
			}else{
				enData = sendData;
			}
		
			int len = enData.getBytes().length;			//전체 전문길이
			String mlen = Integer.toString(len);
			for(int i=0; i < (4 - mlen.length()); i++)
				mlen = "0" + mlen;
			
			String logStr = mlen	+	enData ;
			
			bw.write(	logStr	 );
			bw.flush();
			//logger.info("leng = " + logStr.getBytes().length);logger.info("enData = " + enData);
			logger.info("ToServer = " + logStr);
			
		} catch (IOException ioe) {
			logger.info("send error = " + ioe);
			logger.info("host = " + host);
			logger.info("port " + port);
			return false;
		}
		return true;		
	}
	
	public boolean TCsend(String sendData) {		

		try
		{
			output = recSocket.getOutputStream();
			BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(output));
			
			
			int len = sendData.getBytes().length;			//전체 전문길이
			String mlen = Integer.toString(len);
			for(int i=0; i < (4 - mlen.length()); i++)
				mlen = "0" + mlen;
				
			bw.write(mlen+sendData);
			bw.flush();	
			socket.close();	
			recSocket.close();	
			
			logger.info("ToCli = " + sendData);
		} catch (IOException ioe) {
			logger.info("send error = " + ioe);
			return false;
		}
		return true;		
	}

	/**
	 * SEED 복호화
	 * @param data 복호화처리용 데이터
	 * @param key	키값
	 * @return	seed decrypt 처리된 데이터
	 */
	public byte[] seedDecrypt(byte[] data, String key) {
		return SymmetricCipher.SEED_CBC_DECRYPT(	data,	key.getBytes()		);
	}

	/**
	 * SEED 암호화
	 * @param data	암호화 처리용 데이터
	 * @param key	키값
	 * @return		seed encrypt 처리된 데이터
	 */
	public byte[] seedEncrypt(String data, String key) {
		return SymmetricCipher.SEED_CBC_ENCRYPT(	data.getBytes(),	key.getBytes()		);
	}

	/**
	 * Base64 Decoding
	 * @param enData	인코딩 데이터
	 * @return			디코딩 데이터
	 */
	public byte[] base64Decoding(byte[] enData) {
		return Base64.base64Decode(enData);
	}

	/**
	 * Base64 Encoding
	 * @param deData	입력 데이터
	 * @return			인코딩 데이터
	 */
	public byte[] base64Encoding(byte[] deData) {
		return Base64.base64Encode(deData);
	}
	/**
	 * 디코딩 데이터 조회
	 * @return	디코딩 데이터
	 */
	public String getDeData() {
		return deData;
	}

	/**
	 * 인코딩 데이터 조회
	 * @return	인코딩 데이터
	 */
	public String getEnData() {
		return enData;
	}
		 
	 /**
	 * 디코딩 데이터 설정
	 * @param string	입력데이터
	 */
	public void setDeData(String string) {
		deData = string;
	}

	/**
	 * 인코딩 데이터 설정
	 * @param string	입력데이터
	 */
	public void setEnData(String string) {
		enData = string;
	}
	
	
	public void setKey(String string) {
		key = string;
	}
	
	public void setMessage(String string) {
		
		message = string;
	
	}
	 public static String ByteSubStr( String Pst_Str, int Pi_Off, int Pi_Len )
    	{
	        byte[] Ly_Str = Pst_Str.getBytes();
	        String Lst_Str = new String( Ly_Str, Pi_Off, Pi_Len );
	
	        return Lst_Str;
   	}


	
	
	
	
}