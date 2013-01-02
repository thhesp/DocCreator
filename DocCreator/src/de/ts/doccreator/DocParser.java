package de.ts.doccreator;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.ArrayList;
import java.util.Scanner;

import de.ts.doccreator.model.DocFile;
import de.ts.doccreator.model.DocFunction;
import de.ts.doccreator.model.DocModel;

public class DocParser {
	
	private String path;
	private DocModel doc;
	private ArrayList<File> files;
	private static String encoding = "UTF-8";
	
	public DocParser(String path){
		this.path = path;
		doc = new DocModel();
		files = new ArrayList<File>();
	}
	
	public void parse(){
		searchFiles();
		parseFiles();
	}
	
	public DocModel getDocModel(){
		return doc;
	}
	
	private void searchFiles(){
		addFile(new File(path));
		System.out.println("Files detected");
		System.out.println(files);
	}
	
	private void addFile(File file){
	    File[] children = file.listFiles();
	    if (children != null) {
	        for (File child : children) {
	        	if(!child.getName().contains(".svn") && !child.getName().contains("~")){
		        	if(child.getName().contains(".php") || 
		        			child.getName().contains(".js")|| 
		        			child.getName().contains(".java"))
		            files.add(child);
		            addFile(child);
	        	}
	        }
	    }
	}
	
	private void parseFiles(){
		DocFile file;
		for (File f : files){
			try {
				file = parseFile(f);
				doc.addFile(file);
			} catch (FileNotFoundException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
	
	private DocFile parseFile(File file) throws FileNotFoundException{
		String filepath = file.getPath();
		String comparepath = path.replace("/", ".").replace("\\",".");
		filepath = filepath.replace("/", ".").replace("\\",".");
		filepath = filepath.replace(comparepath, "");
		int end = filepath.lastIndexOf(".");
		String filename = filepath.substring(1, end);
		
		DocFile docFile = new DocFile(filename);
	    StringBuilder text = new StringBuilder();
	    String NL = System.getProperty("line.separator");
	    Scanner scanner = new Scanner(file, encoding);
	    try {
	    	boolean found = false;
	    	String temp = "";
	      while (scanner.hasNextLine()){
	    	  String line = scanner.nextLine();
	    	  if(line.contains("/**")){
	    		  found = true;
	    	  }
	    	  if(found){
	    		  temp += line +NL;
	    		  if(line.contains("**/")){
	    			  found = false;
	    			  text.append(temp);
	    			  temp = "";
	    		  }
	    	  }
	      }
	    }
	    finally{
	      scanner.close();
	    }
	    
	    
		FileParser parser = new FileParser(text.toString(), file.getName());
		docFile.setDescription(parser.getFileDescription());
		DocFunction[] functions = parser.getFunctions();
		
		for(int i = 0; i < functions.length; i++){
			docFile.addFunction(functions[i]);
		}
		
		return docFile;
	}

}
