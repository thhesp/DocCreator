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
		String filename = file.getName();
		int end = filename.indexOf(".");
		filename = filename.substring(0, end);
		
		DocFile docFile = new DocFile(filename);
	    StringBuilder text = new StringBuilder();
	    String NL = System.getProperty("line.separator");
	    Scanner scanner = new Scanner(file, encoding);
	    try {
	      while (scanner.hasNextLine()){
	        text.append(scanner.nextLine() + NL);
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
