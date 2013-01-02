package de.ts.doccreator;

public class DocCreator {
	
	public static void main(String [] argv){
		String input = "./in";
		String output = "./out";
		/*if(argv[0] != null){
			input = argv[0];
		}
		if(argv[1] != null){
			output = argv[1];
		}*/
		
		DocParser parser = new DocParser(input);
		parser.parse();
		
		DocGenerator generator = new DocGenerator(parser.getDocModel(), output);
		generator.generateDoc();
	}

}
